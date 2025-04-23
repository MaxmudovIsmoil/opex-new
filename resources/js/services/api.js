import apiClient from "../axios";
// import apiClient from '../plugins/axios';

export const getAllUsers = async () => {
    try {
        // const response = await apiClient.get("/admin/user/all");
        const response = await apiClient.get("/admin/user/all");
        return response.data;
    } catch (error) {
        console.error("Xatolik:", error);
        throw error;
    }
};

export default apiClient;