import Vue from "vue";
import ContractServices from "../services/ContractServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getContracts(context, payload) {
        const { data } = await ContractServices.index(payload);
        return data;
    },
    async filterContracts(context, payload) {
        const { data } = await ContractServices.filter(payload);
        return data;
    },
    async getContract(context, payload) {
        const { data } = await ContractServices.show(payload);
        return data;
    },
    async createContract(context, payload) {
        const { data } = await ContractServices.create(payload);
        return data;
    },
    async updateContract(context, payload) {
        const { data } = await ContractServices.update(payload);
        return data;
    },
    async deleteContract(context, payload) {
        const { data } = await ContractServices.delete(payload);
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