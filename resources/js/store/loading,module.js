

const initialState = {
    isLoading : false,
};

export const state = { ...initialState };

export const actions = {
    async showLoadingAction(context, payload) {
        context.commit("setLoadingStatus", true);

    },

    async endLoadingAction(context, payload) {
        context.commit("setLoadingStatus", false);

    },
};

export const mutations = {
    setLoadingStatus(state, status){
        state.isLoading = status;
    },
};

const getters = {
    isLoading(state) {
        return state.isLoading;
    },
};

export default {
    state,
    actions,
    mutations,
    getters
};