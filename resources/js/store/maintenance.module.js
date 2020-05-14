import Vue from "vue";
import MaintenanceServices from "../services/MaintenanceServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getMaintenances(context, payload) {
        const { data } = await MaintenanceServices.index(payload);
        return data;
    },
    async filterMaintenances(context, payload) {
        const { data } = await MaintenanceServices.filter(payload);
        return data;
    },
    async getMaintenance(context, payload) {
        const { data } = await MaintenanceServices.show(payload);
        return data;
    },
    async createMaintenance(context, payload) {
        const { data } = await MaintenanceServices.create(payload);
        return data;
    },
    async updateMaintenance(context, payload) {
        const { data } = await MaintenanceServices.update(payload);
        return data;
    },
    async deleteMaintenance(context, payload) {
        const { data } = await MaintenanceServices.delete(payload);
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