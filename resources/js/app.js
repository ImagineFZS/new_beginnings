require('./bootstrap');

import { createApp } from 'vue';
import router from "./routes";
import Index from "./index.vue";
import moment from 'moment';
import StarRating from "./shared/components/StarRating.vue";
import FatalError from "./shared/components/FatalError.vue";
import ValidationErrors from "./shared/components/ValidationErrors.vue";
import Success from "./shared/components/Success.vue";

import { library } from '@fortawesome/fontawesome-svg-core';
import { faStar } from "@fortawesome/free-solid-svg-icons";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(faStar)
library.add(fas)

const app = createApp({})

app.use(router)

app.component('Index', Index)
app.component('star-rating', StarRating)
app.component('font-awesome-icon', FontAwesomeIcon)
app.component('fatal-error', FatalError);
app.component('v-errors', ValidationErrors);
app.component('success', Success);

app.mount('#app')

app.mixin({
    methods: {
        fromToday(value) {
            return moment(value).fromNow();
        }
    }
});
