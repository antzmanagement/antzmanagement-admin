import Vue from "vue";
import ClaimServices from "../services/ClaimServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getClaims(context, payload) {
        const { data } = await ClaimServices.index(payload);
        return data;
    },
    async filterClaims(context, payload) {
        const { data } = await ClaimServices.filter(payload);
        return data;
    },
    async getClaim(context, payload) {
        const { data } = await ClaimServices.show(payload);
        return data;
    },
    async createClaim(context, payload) {
        const { data } = await ClaimServices.create(payload);
        return data;
    },
    async updateClaim(context, payload) {
        const { data } = await ClaimServices.update(payload);
        return data;
    },
    async deleteClaim(context, payload) {
        const { data } = await ClaimServices.delete(payload);
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