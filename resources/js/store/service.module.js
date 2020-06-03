
import ServiceServices from "../services/ServiceServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getServices(context, payload) {
        const { data } = await ServiceServices.index(payload);
        return data;
    },
    async filterServices(context, payload) {
        const { data } = await ServiceServices.filter(payload);
        return data;
    },
    async getService(context, payload) {
        const { data } = await ServiceServices.show(payload);
        return data;
    },
    async createService(context, payload) {
        const { data } = await ServiceServices.create(payload);
        return data;
    },
    async updateService(context, payload) {
        const { data } = await ServiceServices.update(payload);
        return data;
    },
    async deleteService(context, payload) {
        const { data } = await ServiceServices.delete(payload);
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