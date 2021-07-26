import Vue from "vue";
import RoomCheckServices from "../services/RoomCheckServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getRoomChecks(context, payload) {
        const { data } = await RoomCheckServices.index(payload);
        return data;
    },
    async filterRoomChecks(context, payload) {
        const { data } = await RoomCheckServices.filter(payload);
        return data;
    },
    async getRoomCheck(context, payload) {
        const { data } = await RoomCheckServices.show(payload);
        return data;
    },
    async createRoomCheck(context, payload) {
        const { data } = await RoomCheckServices.create(payload);
        return data;
    },
    async updateRoomCheck(context, payload) {
        const { data } = await RoomCheckServices.update(payload);
        return data;
    },
    async deleteRoomCheck(context, payload) {
        const { data } = await RoomCheckServices.delete(payload);
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