import Vue from "vue";
import CleaningServices from "../services/CleaningServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getCleanings(context, payload) {
        const { data } = await CleaningServices.index(payload);
        return data;
    },
    async filterCleanings(context, payload) {
        const { data } = await CleaningServices.filter(payload);
        return data;
    },
    async getCleaning(context, payload) {
        const { data } = await CleaningServices.show(payload);
        return data;
    },
    async createCleaning(context, payload) {
        const { data } = await CleaningServices.create(payload);
        return data;
    },
    async updateCleaning(context, payload) {
        const { data } = await CleaningServices.update(payload);
        return data;
    },
    async deleteCleaning(context, payload) {
        const { data } = await CleaningServices.delete(payload);
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