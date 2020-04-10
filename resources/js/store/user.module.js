import Vue from "vue";
import UserServices from "../services/UserServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getUsers(context, payload) {
        const { data } = await UserServices.index(payload);
        return data;
    },
    async getUser(context, payload) {
        const { data } = await UserServices.show(payload);
        return data;
    },
    async createUser(context, payload) {
        const { data } = await UserServices.create(payload);
        return data;
    },
    async updateUser(context, payload) {
        const { data } = await UserServices.update(payload);
        return data;
    },
    async deleteUser(context, payload) {
        const { data } = await UserServices.delete(payload);
        return data;
    },
    async changePassword(context, payload) {
        const { data } = await UserServices.changePassword(payload);
        return data;
    },
    async checkPassword(context, payload) {
        const { data } = await UserServices.checkPassword(payload);
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