import Vue from "vue";
import PaymentServices from "../services/PaymentServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getPayments(context, payload) {
        const { data } = await PaymentServices.index(payload);
        return data;
    },
    async filterPayments(context, payload) {
        const { data } = await PaymentServices.filter(payload);
        return data;
    },
    async getPayment(context, payload) {
        const { data } = await PaymentServices.show(payload);
        return data;
    },
    async createPayment(context, payload) {
        const { data } = await PaymentServices.create(payload);
        return data;
    },
    async updatePayment(context, payload) {
        const { data } = await PaymentServices.update(payload);
        return data;
    },
    async deletePayment(context, payload) {
        const { data } = await PaymentServices.delete(payload);
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