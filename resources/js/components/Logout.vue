<template>
  <a class="nav-link fw-semibold" href="javascript:void(0);" @click="logout">
    <font-awesome-icon :icon="['fas', 'right-from-bracket']" /> {{ $t('sign_out') }}
  </a>
</template>

<script>
  import Cookies from "js-cookie";
  import { useRouter } from "vue-router";

  export default {
    name: "Logout",
    setup() {
      const router = useRouter();

      const logout = () => {
        Cookies.remove("token"); // Tokenni o‘chirish
        router.push("/login");
      };

      return { logout };
    },
  };
</script>
