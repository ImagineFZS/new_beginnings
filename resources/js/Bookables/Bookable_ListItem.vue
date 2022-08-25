<template>
    <div class="card w-100">
        <div class="card-body">
            <router-link :to="{name : 'Bookable', params : {id} }">
                <h5 class="card-title"> <h1>{{title}}</h1></h5>
            </router-link>
            <p class="card-text">{{description}}</p>
        </div>
    </div>
</template>

<script>
export default{
    props: {'title' : String, 'description' : String, 'id' : Number}
}
</script>

