<template>
    <div>
        <success v-if="success">Checkout Successful</success>
        <div class="row" v-else>
            <div class="col-md-8" v-if="itemsInBasket">

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="first_names">First Name</label>
                        <input type="text" class="form-control" name="first_names" v-model="customer.first_names" :class="[{'is-invalid': this.errorFor('customer.first_names')}]"/>
                        <v-errors :errors="errorFor('customer.first_names')"></v-errors>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" name="last_name" v-model="customer.last_name" :class="[{'is-invalid': this.errorFor('customer.last_name')}]"/>
                        <v-errors :errors="errorFor('customer.last_name')"></v-errors>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" v-model="customer.email" :class="[{'is-invalid': this.errorFor('customer.email')}]"/>
                        <v-errors :errors="errorFor('customer.email')"></v-errors>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="street">Street Address</label>
                        <input type="text" class="form-control" name="street" v-model="customer.street" :class="[{'is-invalid': this.errorFor('customer.street')}]"/>
                        <v-errors :errors="errorFor('customer.street')"></v-errors>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="city" v-model="customer.city" :class="[{'is-invalid': this.errorFor('customer.city')}]"/>
                        <v-errors :errors="errorFor('customer.city')"></v-errors>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="country">Country</label>
                        <input type="text" class="form-control" name="country" v-model="customer.country" :class="[{'is-invalid': this.errorFor('customer.country')}]"/>
                        <v-errors :errors="errorFor('customer.country')"></v-errors>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="state">Province</label>
                        <input type="text" class="form-control" name="state" v-model="customer.state" :class="[{'is-invalid': this.errorFor('customer.state')}]"/>
                        <v-errors :errors="errorFor('customer.state')"></v-errors>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="zip">Zip Code</label>
                        <input type="text" class="form-control" name="zip" v-model="customer.zip" :class="[{'is-invalid': this.errorFor('customer.zip')}]"/>
                        <v-errors :errors="errorFor('customer.zip')"></v-errors>
                    </div>
                </div>

                <hr/>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <button style="width: 100%" type="submit" class="btn btn-lg btn-primary btn-block" @click.prevent="book" :disabled="loading">Checkout</button>
                    </div>
                </div>

            </div>
            <div class="col-md-8" v-else>
                <div class="jumbotron jumbotron-fluid text-center">
                    <h1>Empty</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="d-flex justify-content-between">
                    <h6 class="text-uppercase text-secondary font-weight-bolder">Cart</h6>
                    <h6 style="color: black" class="badge badge-secondary text-uppercase">
                        <span v-if="itemsInBasket">Items: {{ itemsInBasket }}</span>
                        <span v-else>Empty</span>
                    </h6>
                </div>

                <div v-for="item in basket" :key="item.bookable.id">
                    <div class="pt-2 pb-2 border-top d-flex justify-content-between">
                        <span>
                            <router-link :to="{name: 'Bookable', params: {id: item.bookable.id}}">{{item.bookable.title}}</router-link>
                        </span>
                        <span class="font-weight-bold">
                            ${{ item.price.total }}
                        </span>
                    </div>

                    <div class="pt-2 pb-2 d-flex justify-content-between">
                        <span>
                            From: {{ item.dates.from }}
                        </span>
                        <span>
                            To: {{ item.dates.to }}
                        </span>
                    </div>

                    <div class="pt-2 pb-2 text-right">
                        <button class="btn btn-sm btn-outline-secondary" @click="$store.dispatch('removeFromBasket', item.bookable.id)">
                            X
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapState } from 'vuex';
import validateErrors from '../shared/mixins/validateErrors';

export default{
    mixins: [validateErrors],
    data(){
        return{
            loading: false,
            bookingAttempted: false,
            customer:{
                first_names: null,
                last_name: null,
                email: null,
                street: null,
                city: null,
                country: null,
                state: null,
                zip: null
            }
        }
    },
    computed:{
        ...mapGetters(["itemsInBasket"]),
        ...mapState({
            basket: state => state.basket.items
        }),
        success(){
            return !this.loading && (this.itemsInBasket == 0) && this.bookingAttempted;
        }
    },
    methods:{
        async book(){
            this.loading = true;
            this.errors = null;
            this.bookingAttempted = false;

            try{

                await axios.post(`/api/checkout`, {
                    customer: this.customer,
                    bookings: this.basket.map(basketItem => ({
                         bookable_id: basketItem.bookable.id,
                         from: basketItem.dates.from,
                         to: basketItem.dates.to
                    }))
                });

                this.$store.dispatch("clearBasket");

            }catch(error){
                this.errors = error.response && error.response.data.errors;
            }

            this.loading = false;
            this.bookingAttempted = true;
        }
    }
}
</script>

<style scoped>
h6.badge{
    font-size: 100%;
}

a{
    color: black;
}
</style>

