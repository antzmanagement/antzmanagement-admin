import Vue from "vue";
import ReportServices from "../services/ReportServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getReports(context, payload) {
        const { data } = await ReportServices.index(payload);
        return data;
    },
    async filterReports(context, payload) {
        const { data } = await ReportServices.filter(payload);
        return data;
    },
    async getReport(context, payload) {
        const { data } = await ReportServices.show(payload);
        return data;
    },
    async createReport(context, payload) {
        const { data } = await ReportServices.create(payload);
        return data;
    },
    async updateReport(context, payload) {
        const { data } = await ReportServices.update(payload);
        return data;
    },
    async deleteReport(context, payload) {
        const { data } = await ReportServices.delete(payload);
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