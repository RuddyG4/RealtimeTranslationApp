import router from "@/router";

export default {
    namespaced: true,
    state: {
        authenticated: false,
        user: {},
    },
    getters: {
        authenticated(state) {
            return state.authenticated;
        },
        user(state) {
            return state.user;
        },
    },
    mutations: {
        setAuthenticated(state, value) {
            state.authenticated = value;
        },
        setUser(state, value) {
            state.user = value;
        },
    },
    actions: {
        login({ commit }) {
            return axios
                .get("/api/user")
                .then(({ data }) => {
                    commit("setUser", data);
                    commit("setAuthenticated", true);
                    router.push({ name: "dashboard" });
                })
                .catch(({ response: { data } }) => {
                    commit("setUser", {});
                    commit("setAuthenticated", false);
                });
        },
        logout({ commit }) {
            commit("setUser", {});
            commit("setAuthenticated", false);
        },
    },
};
