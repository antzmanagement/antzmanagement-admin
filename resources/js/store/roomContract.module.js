import RoomContractServices from "../services/RoomContractServices";

// const initialState = {
// };

// export const state = { ...initialState };

export const actions = {
    async getRoomContracts(context, payload) {
        const { data } = await RoomContractServices.index(payload);
        return data;
    },
    async filterRoomContracts(context, payload) {
        const { data } = await RoomContractServices.filter(payload);
        return data;
    },
    async getRoomContract(context, payload) {
        const { data } = await RoomContractServices.show(payload);
        return data;
    },
    async createRoomContract(context, payload) {
        const { data } = await RoomContractServices.create(payload);
        return data;
    },
    async updateRoomContract(context, payload) {
        const { data } = await RoomContractServices.update(payload);
        return data;
    },
    async deleteRoomContract(context, payload) {
        const { data } = await RoomContractServices.delete(payload);
        return data;
    },
    async transferRoomContract(context, payload) {
        const { data } = await RoomContractServices.transferRoomContract(payload);
        return data;
    },
    async checkoutRoomContract(context, payload) {
        const { data } = await RoomContractServices.checkoutRoomContract(payload);
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