<template>
    <div class="row">
        <div class="col-md-8 pb-4">
            <div class="card">
                <div class="card-body">
                    <div v-if="!loading">
                        <h2> {{ bookable.title }} </h2>
                        <hr />
                        <article> {{ bookable.description }} </article>
                    </div>
                    <div v-else>Loading...</div>
                </div>
            </div>

        <review-list :bookableID="this.$route.params.id"></review-list>

        </div>
        <div class="col-md-4 pb-4">
            <availability :bookableID="this.$route.params.id" @availability="checkPrice($event)" class="mb-4"></availability>

            <Transition name="fade">
                <price-breakdown v-if="price" :price="price" class="mb-4"></price-breakdown>
            </Transition>

            <Transition name="fade">
                <button class="btn btn-outline-secondary btn-block col-12" v-if="price" @click="addToBasket" :disabled="inBasketAlready">Book</button>
            </Transition>

            <br><br>

            <button class="btn btn-outline-secondary btn-block col-12" v-if="inBasketAlready" @click="removeFromBasket">Remove from Basket</button>

            <div v-if="inBasketAlready" class="mt-4 text-muted warning">Already in basket</div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';

import Availability from "./Availability";
import ReviewList from "./ReviewList";
import PriceBreakdown from "./PriceBreakDown";
import { mapState } from 'vuex';

export default{

    components: {
        Availability,
        ReviewList,
        PriceBreakdown
    },

   data() {
        return{
            bookable: null,
            loading: false,
            price: null
        };
    },
    created()
    {
        this.loading = true;
        var id = this.$route.params.id;
        axios.get(`/api/bookables/${id}`).then(response => {this.bookable = response.data.data, this.loading = false});
    },
    methods:{
        async checkPrice(hasAvailability){
            if(!hasAvailability){
                this.price = null;
                return;
            }

            try{

                this.price = (await axios.get(`/api/bookables/${this.bookable.id}/price?from=${this.lastSearch.from}&to=${this.lastSearch.to}`)).data.data;

            }catch(error){
                this.price = null;
            }
        },
        addToBasket(){
            this.$store.dispatch('addToBasket', {
                bookable: this.bookable,
                price: this.price,
                dates: this.lastSearch
            });
        },
        removeFromBasket(){
            this.$store.dispatch("removeFromBasket", this.bookable.id);
        }
    },
    computed: {
        ...mapState({
            lastSearch: "lastSearch"
        }),
        inBasketAlready(){
            if(this.bookable == null){
                return false;
            }

            return this.$store.getters.inBasketAlready(this.bookable.id);
        }
    }
}
</script>

<style scoped>
.warning{
    font-size: 0.7rem;
}
</style>

