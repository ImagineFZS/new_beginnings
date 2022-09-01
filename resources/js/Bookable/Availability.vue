<template>
    <div>
        <h6 class="text-uppercase text-secondary font-weight-bolder">Check Availability

            <Transition name="fade">
                <span v-if="noAvailability" class="text-danger">(Not Available!)</span>
            </Transition>

            <Transition name="fade">
                <span v-if="hasAvailability" class="text-success">(Available!)</span>
            </Transition>

        </h6>

    <div class="row">
        <div class="form-group col-md-6">
            <label for="from">From</label>
            <input type="text" name="from" class="form-control form-control-sm" placeholder="Start Date" v-model="from" @keyup.enter="check" :class="[{'is-invalid': this.errorFor('from')}]">

            <v-errors :errors="errorFor('from')"></v-errors>

        </div>

        <div class="form-group col-md-6">
            <label for="to">To</label>
            <input type="text" name="to" class="form-control form-control-sm" placeholder="End Date" v-model="to" @keyup.enter="check" :class="[{'is-invalid': this.errorFor('to')}]">

            <v-errors :errors="errorFor('to')"></v-errors>

        </div>
    </div>

    <br>

    <button class="btn btn-secondary col-12" @click="check" :disabled="loading">Check</button>

    </div>
</template>

<script>

import { is422 } from "./../shared/utils/response";
import validationErrors from "./../shared/mixins/validateErrors";

export default{
    mixins: [validationErrors],
    emits: ["availability"],
    props:{
        bookableID: [String, Number]
    },
    data(){
        return {
            from: this.$store.state.lastSearch.from,
            to: this.$store.state.lastSearch.to,
            loading: false,
            status: null,
            errors: null,
        }
    },
    methods: {
        async check(){
            this.loading = true;
            this.errors = null;

            this.$store.dispatch('setLastSearch', {
                from: this.from,
                to: this.to
            });

            try{

                this.status = (await axios.get(`/api/bookables/${this.bookableID}/availability?from=${this.from}&to=${this.to}`)).status;
                this.$emit("availability", this.hasAvailability);

            }catch(error){

                if(is422(error))
                {
                    this.errors = error.response.data.errors;
                }
                this.status = error.response.status;
                this.$emit("availability", this.hasAvailability);
            }

            this.loading = false;

        },
        errorFor(field)
        {
            return this.hasErrors && this.errors[field] ? this.errors[field] : null;
        }
    },
    computed:{
        hasErrors(){

            if(this.status == 422 && this.status != null)
            {
                return true;
            }
        },
        hasAvailability()
        {
            if(this.status == 200)
            {
                return true;
            }
        },
        noAvailability()
        {
            if(this.status == 404)
            {
                return true;
            }
        }
    },
    beforeCreate(){
        this.$store.dispatch('loadStoredState');
    }
}
</script>


<style scoped>
    label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: gray;
        font-weight: bolder;
    }

    .is-invalid {
        border-color: #b22222;
        background-image: none;
    }

    .invalid-feedback {
        color: #b22222;
    }

</style>
