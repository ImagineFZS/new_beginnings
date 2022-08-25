<template>
    <div>

        <div class="row mb-4" v-for="row in rows" :key="'row' + row">

            <div class="col d-flex align-items-stretch" v-for="(b, column) in bookablesInRow(row)" :key="'row' + row + column">

                <bookable-list-item v-bind="b"></bookable-list-item>

            </div>

            <div class="col" v-for="p in placeHoldersInRow(row)" :key="'placeholder' + row + p"></div>

        </div>


    </div>
</template>

<script>
import BookableListItem from "./Bookable_ListItem.vue";

export default{
    components: {
        BookableListItem
    },
    data() {
        return{
            bookables: null,
            columns: 3
        };
    },
    computed:{
        rows()
        {
            return this.bookables == null ? 0 : Math.ceil(this.bookables.length / this.columns);
        }
    },
    methods: {
        bookablesInRow(row)
        {
            return this.bookables.slice((row - 1) * this.columns, row * this.columns);
        },
        placeHoldersInRow(row)
        {
            return this.columns - this.bookablesInRow(row).length;
        }
    },
    created(){

        const request = axios.get('/api/bookables').then(response => {this.bookables = response.data.data});

    }
};
</script>

