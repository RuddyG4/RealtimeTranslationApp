import { createStore } from "vuex";
import VuexPersistence from "vuex-persist";
import auth from '@/store/auth'

const vuexLocal = new VuexPersistence({
    storage: window.sessionStorage,
});

const store = createStore({
    plugins: [vuexLocal.plugin],
    modules:{
        auth
    }
});
export default store;
