<template>
    <div>
        <div class="d-flex">
            <font-awesome-icon icon="fas fa-star" v-for="star in fullStar" :key="'full' + star" @click="$emit('rating:changed', star)" />
            <font-awesome-icon icon="fas fa-star-half-alt" v-if="halfStar" />
        </div>
    </div>
</template>

<script>

export default {
    props: {
        rating: Number
    },
    computed: {
        halfStar(){
            const fraction = Math.round((this.rating - Math.floor(this.rating)) * 100);

            if(fraction > 0 && fraction < 50)
            {
                return true;

            }else
            {
                return false;
            }
        },
        fullStar(){
            return Math.round(this.rating);
        }
    }
}
</script>

