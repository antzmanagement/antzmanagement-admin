import Vue from "vue";
import RoleServices from "../services/RoleServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getRoles(context, payload) {
        const { data } = await RoleServices.index(payload);
        return data;
    },
    async filterRoles(context, payload) {
        const { data } = await RoleServices.filter(payload);
        return data;
    },
    async getRole(context, payload) {
        const { data } = await RoleServices.show(payload);
        return data;
    },
    async createRole(context, payload) {
        const { data } = await RoleServices.create(payload);
        return data;
    },
    async updateRole(context, payload) {
        const { data } = await RoleServices.update(payload);
        return data;
    },
    async deleteRole(context, payload) {
        const { data } = await RoleServices.delete(payload);
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