import Vue from "vue";
import RoomServices from "../services/RoomServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getRooms(context, payload) {
        const { data } = await RoomServices.index(payload);
        return data;
    },
    async filterRooms(context, payload) {
        const { data } = await RoomServices.filter(payload);
        return data;
    },
    async getRoom(context, payload) {
        const { data } = await RoomServices.show(payload);
        return data;
    },
    async createRoom(context, payload) {
        const { data } = await RoomServices.create(payload);
        return data;
    },
    async updateRoom(context, payload) {
        const { data } = await RoomServices.update(payload);
        return data;
    },
    async deleteRoom(context, payload) {
        const { data } = await RoomServices.delete(payload);
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