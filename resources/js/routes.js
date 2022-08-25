import Bookables from "./Bookables/Bookables.vue";
import Bookable from "./Bookable/Bookable.vue";
import Review from "./Review/Review.vue";
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: "/",
        component: Bookables,
        name: "Home",
    },
    {
        path: "/bookable/:id",
        component: Bookable,
        name: "Bookable",
    },
    {
        path: "/review/:id",
        component: Review,
        name: "Review",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
