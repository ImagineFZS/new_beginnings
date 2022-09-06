<template>
    <div>
        <nav class="navbar bg-white border-bottom navbar-light">
            <router-link class="navbar-brand mr-auto" :to="{name: 'Home'}">Home</router-link>

            <router-link class="btn nav-button" :to="{name: 'basket'}">
                Basket
                <span v-if="itemsInBasket" class="badge badge-secondary" style="color: black">{{ itemsInBasket }}</span>
            </router-link>

        </nav>

        <div class="container mt-4 mb-4 pr-4 pl-4">
            <router-view></router-view>
        </div>

    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default{
    computed:{
        ...mapGetters({
            itemsInBasket: 'itemsInBasket'
        })
    }

}
</script>

