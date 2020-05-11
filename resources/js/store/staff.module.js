import Vue from "vue";
import StaffServices from "../services/StaffServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getStaffs(context, payload) {
        const { data } = await StaffServices.index(payload);
        return data;
    },
    async filterStaffs(context, payload) {
        const { data } = await StaffServices.filter(payload);
        return data;
    },
    async getStaff(context, payload) {
        const { data } = await StaffServices.show(payload);
        return data;
    },
    async createStaff(context, payload) {
        const { data } = await StaffServices.create(payload);
        return data;
    },
    async updateStaff(context, payload) {
        const { data } = await StaffServices.update(payload);
        return data;
    },
    async deleteStaff(context, payload) {
        const { data } = await StaffServices.delete(payload);
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