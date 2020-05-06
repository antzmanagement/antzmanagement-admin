import Vue from "vue";
import OwnerServices from "../services/OwnerServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getOwners(context, payload) {
        const { data } = await OwnerServices.index(payload);
        return data;
    },
    async filterOwners(context, payload) {
        const { data } = await OwnerServices.filter(payload);
        return data;
    },
    async getOwner(context, payload) {
        const { data } = await OwnerServices.show(payload);
        return data;
    },
    async createOwner(context, payload) {
        const { data } = await OwnerServices.create(payload);
        return data;
    },
    async updateOwner(context, payload) {
        const { data } = await OwnerServices.update(payload);
        return data;
    },
    async deleteOwner(context, payload) {
        const { data } = await OwnerServices.delete(payload);
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