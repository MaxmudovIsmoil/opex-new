<template>
    <div class="container d-flex justify-content-center align-items-center" style="height: 90vh">
      <div class="shadow-lg rounded-sm center-block">
        <div class="card border-0 p-5" style="width: 55rem">
          <div class="row g-0">
            <div class="col-6">
              <div class="card-body">
                <form @submit.prevent="submitForm" class="mx-auto" method="POST">
                  <div class="text-start">
                    <h2>Sign in</h2>
                  </div>
                  <div class="row mt-4">
                    <div class="col-10">
                      <div class="form-group mb-3">
                        <label class="form-label">Username</label>
                        <input 
                          type="text" 
                          name="username" 
                          class="form-control" 
                          v-model="username" 
                          required
                        />
                      </div>
                      <div class="form-group mb-3">
                        <label class="form-label">Password</label>
                        <input 
                          type="password" 
                          class="form-control" 
                          name="password" 
                          v-model="password" 
                          required
                        />
                      </div>
                    </div>
                  </div>
                  <button class="btn btn-danger w-8 btn-lg font-500 mt-3" type="submit" :disabled="loading">
                    <span v-if="loading">Loading...</span>
                    <span v-else>Sign in</span>
                  </button>
                </form>
                <p v-if="error" class="text-danger">{{ error }}</p>
                <p v-if="success" class="text-success">{{ success }}</p>
              </div>
            </div>
            <div class="col-6">
              <img style="max-width: 105%; margin-top: 5px;"
                   src="../../files/img/login.png"
                   alt="Logo" 
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import apiClient from "@/axios";
  import Cookies from "js-cookie";
  import { useRouter } from "vue-router";
  import { ref } from "vue";
  
  export default {
    setup() {
      const router = useRouter();
      const username = ref("");
      const password = ref("");
      const loading = ref(false);
      const error = ref("");
      const success = ref("");
  
      const submitForm = async () => {
        loading.value = true;
        error.value = "";
        success.value = "";
  
        try {
          const response = await apiClient.post(
            "/login",
            { username: username.value, password: password.value },
            { withCredentials: true } // Cookie yuborish
          );
  
          const { token, user } = response.data.data;
  
          if (!token) throw new Error("Token not found in response");
  
          // Token va foydalanuvchini cookie'ga saqlash
          Cookies.set("token", token, { expires: 7, secure: true, sameSite: "Strict" });
          Cookies.set("user", JSON.stringify(user), { expires: 1, secure: true });
  
          success.value = "Login successful!";
          router.push("/orders");
        } catch (err) {
          error.value = err.response?.data?.message || "Login failed!";
        } finally {
          loading.value = false;
        }
      };

      return { username, password, loading, error, success, submitForm };
    },
  };
</script>
  