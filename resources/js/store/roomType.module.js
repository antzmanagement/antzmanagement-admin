import Vue from "vue";
import RoomTypeServices from "../services/RoomTypeServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getRoomTypes(context, payload) {
        const { data } = await RoomTypeServices.index(payload);
        return data;
    },
    async filterRoomTypes(context, payload) {
        const { data } = await RoomTypeServices.filter(payload);
        return data;
    },
    async getRoomType(context, payload) {
        const { data } = await RoomTypeServices.show(payload);
        return data;
    },
    async createRoomType(context, payload) {
        const { data } = await RoomTypeServices.create(payload);
        return data;
    },
    async updateRoomType(context, payload) {
        const { data } = await RoomTypeServices.update(payload);
        return data;
    },
    async deleteRoomType(context, payload) {
        const { data } = await RoomTypeServices.delete(payload);
        return data;
    },

    async getPublicRoomTypes(context, payload) {
        const { data } = await RoomTypeServices.getPublicRoomTypes(payload);
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