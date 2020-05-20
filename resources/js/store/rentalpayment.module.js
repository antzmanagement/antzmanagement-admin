import Vue from "vue";
import RentalPaymentServices from "../services/RentalPaymentServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getRentalPayments(context, payload) {
        const { data } = await RentalPaymentServices.index(payload);
        return data;
    },
    async filterRentalPayments(context, payload) {
        const { data } = await RentalPaymentServices.filter(payload);
        return data;
    },
    async getRentalPayment(context, payload) {
        const { data } = await RentalPaymentServices.show(payload);
        return data;
    },
    async createRentalPayment(context, payload) {
        const { data } = await RentalPaymentServices.create(payload);
        return data;
    },
    async updateRentalPayment(context, payload) {
        const { data } = await RentalPaymentServices.update(payload);
        return data;
    },
    async deleteRentalPayment(context, payload) {
        const { data } = await RentalPaymentServices.delete(payload);
        return data;
    },
    async makePayment(context, payload) {
        const { data } = await RentalPaymentServices.makePayment(payload);
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