<template>
    <nav class="navbar navbar-expand-lg navbar-shadow px-2" style="margin-bottom: 35px;">
        <div class="container-fluid">
          <a class="navbar-brand d-flex" href="#">
              <img src="../../files/icons/ET-logo-en.png" alt="Logo" style="width: 35px; height: 30px; margin-top: 8px;"> 
              <div class="project-name">{{ appName }}</div>
          </a>
        
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                  aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span> {{ $t('administrator') }}
          </button>

          <div class="navbar-collapse" id="navbarSupportedContent">

              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <a id="subNav-12" class="nav-link fw-semibold dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Administrator <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li :class="{ 'menu-active': $route.path === '/admin/users' }">
                        <router-link class="dropdown-item fw-semibold " to="/admin/users">{{ $t('users') }}</router-link>
                    </li>
                    <li :class="{ 'menu-active': $route.path === '/admin/instances' }">
                      <router-link class="dropdown-item fw-semibold" to="/admin/instances">{{ $t('instances') }}</router-link>
                    </li>
                    <li :class="{ 'menu-active': $route.path === '/admin/departments' }">
                      <router-link class="dropdown-item fw-semibold " to="/admin/departments">{{ $t('departments') }}</router-link>
                    </li>

                    <li :class="{ 'menu-active': $route.path === '/admin/road-maps' }">
                      <router-link class="dropdown-item fw-semibold " to="/admin/road-maps">{{ $t('roadMap') }}</router-link>
                    </li>
                  </ul>
                </li>

                <li class="nav-link">
                    <router-link class="dropdown-item fw-semibold" to="/orders">{{ $t('portal') }}</router-link>
                </li>

                <li class="nav-link" :class="{ 'menu-active': $route.path === '/orders' }">
                    <router-link class="dropdown-item fw-semibold " to="/orders">{{ $t('orders') }}</router-link>
                </li>
              </ul>

              <ul class="navbar-nav ms-auto">
                  <li class="nav-item">
                      <a class="nav-link fw-semibold" href="javascript:void(0);">
                        <font-awesome-icon :icon="['fas', 'user']" /> Abdujalilov
                      </a>
                  </li>
                  <li class="dropdown">
                      <a class="dropdown-toggle nav-link fw-semibold" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <font-awesome-icon :icon="['fas', 'earth-asia']" /> {{ $t('language') }}  </a>
                          <ul class="dropdown-menu">
                            <li class="nav-link p-0">
                              <button 
                                class="dropdown-item fw-semibold" 
                                @click="changeLanguage('en')"
                              >
                                <img src="../../files/img/usa-flag.png" class="lang-flag mr-2" alt="usa-flag" /> 
                                <span class="pl-2"
                                :class="{ 'menu-active': selectedLang === 'en' }" 
                                >{{ $t('english') }}</span>
                              </button>
                            </li>
                            <li class="nav-link p-0">
                              <button 
                                class="dropdown-item fw-semibold" 
                                @click="changeLanguage('ru')"
                              >
                                <img src="../../files/img/russian-flag.png" class="lang-flag mr-2" alt="russian-flag" /> 
                                <span class="pl-2" 
                                :class="{ 'menu-active': selectedLang === 'ru' }"
                                >{{ $t('russian') }}</span>
                              </button>
                            </li>
                          </ul>

                  </li>

                  <Logout />
                  
              </ul>
          </div>
        </div>
    </nav>

    <router-view />

</template>

<script>
import Logout from '../../components/Logout.vue';

export default {
  name: 'Header',
  components: {
    Logout
  },
  computed: {
    currentPath() {
      return this.$route.path;
    }
  },
  data() {
    return {
      appName: import.meta.env.VITE_APP_NAME,
      selectedLang: localStorage.getItem('lang') || 'en',
    };
  },
  methods: {
    changeLanguage(lang) {
      this.selectedLang = lang; // LOCAL VARIABLGA SAQLASH
      this.$i18n.locale = lang;
      localStorage.setItem('lang', lang); // LOCALSTORAGE'GA SAQLASH
    },
  },
  created() {
    // LOCALSTORAGE'DAGI TILNI SET QILISH
    const savedLang = localStorage.getItem('lang');
    if (savedLang) {
      this.selectedLang = savedLang;
      this.$i18n.locale = savedLang;
    }
  }
};
</script>
