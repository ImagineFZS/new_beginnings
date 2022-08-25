<template>
    <div>
        <h6 class="text-uppercase text-secondary font-weight-bolder">Check Availability
            <span v-if="noAvailability" class="text-danger">(Not Available!)</span>
            <span v-if="hasAvailability" class="text-success">(Available!)</span>
        </h6>

    <div class="row">
        <div class="form-group col-md-6">
            <label for="from">From</label>
            <input type="text" name="from" class="form-control form-control-sm" placeholder="Start Date" v-model="from" @keyup.enter="check" :class="[{'is-invalid': this.errorFor('from')}]">

            <div class="invalid-feedback" v-for="(error, index) in this.errorFor('from')" :key="'from' + index">{{error}}</div>

        </div>

        <div class="form-group col-md-6">
            <label for="to">To</label>
            <input type="text" name="to" class="form-control form-control-sm" placeholder="End Date" v-model="to" @keyup.enter="check" :class="[{'is-invalid': this.errorFor('to')}]">

            <div class="invalid-feedback" v-for="(error, index) in this.errorFor('to')" :key="'to' + index">{{error}}</div>

        </div>
    </div>

    <br>

    <button class="btn btn-secondary col-12" @click="check" :disabled="loading">Check</button>

    </div>
</template>

<script>
export default{
    props:{
        bookableID: String
    },
    data(){
        return {
            from: null,
            to: null,
            loading: false,
            status: null,
            errors: null,
        }
    },
    methods: {
        check(){
            this.loading = true;
            this.errors = null;
            axios.get(`/api/bookables/${this.bookableID}/availability?from=${this.from}&to=${this.to}`
            ).then(response => {
                this.status = response.status;

            }).catch(error => {
                if(error.response.status == 422)
                {
                    this.errors = error.response.data.errors;
                }
                this.status = error.response.status;

            }).then(() => this.loading = false);
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
                return 422;
            }
        },
        hasAvailability()
        {
            if(this.status == 200)
            {
                return 200;
            }
        },
        noAvailability()
        {
            if(this.status == 404)
            {
                return 404;
            }
        }
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
