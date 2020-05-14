import PropertyServices from "../services/PropertyServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getProperties(context, payload) {
        const { data } = await PropertyServices.index(payload);
        return data;
    },
    async filterProperties(context, payload) {
        const { data } = await PropertyServices.filter(payload);
        return data;
    },
    async getProperty(context, payload) {
        const { data } = await PropertyServices.show(payload);
        return data;
    },
    async createProperty(context, payload) {
        const { data } = await PropertyServices.create(payload);
        return data;
    },
    async updateProperty(context, payload) {
        const { data } = await PropertyServices.update(payload);
        return data;
    },
    async deleteProperty(context, payload) {
        const { data } = await PropertyServices.delete(payload);
        return data;
    },
};

// export const mutations = {
// };

// const getters = {
//     article(state) {
//         return state.article;
//     },
//     comments(state) {
//         return state.comments;
//     }
// };

export default {
    // state,
    actions,
    // mutations,
    // getters
};