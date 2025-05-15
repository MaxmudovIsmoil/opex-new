import { createRouter, createWebHistory } from 'vue-router';
import Cookies from "js-cookie";
import Instances from './views/admin/instance/index.vue';
import Users from './views/admin/user/index.vue';
import Department from './views/admin/department/index.vue';
import RoadMap from './views/admin/roadMap/index.vue';
import Orders from './views/order/index.vue';
import OrderDetail from './views/orderDetail/index.vue';
import Layout from './views/layouts/Layout.vue';
import Login from './views/login/Login.vue';

const routes = [
    { path: '/login', name: 'Login', component: Login },
    {
        path: '/',
        redirect:'orders',
        component: Layout,
        children: [
            { 
                path: 'admin/users', 
                name: 'Users', 
                component: Users,
                meta: { requiresAuth: true, rule: "ADMIN" },
            },
            {
                path: 'admin/instances', 
                name: 'Instances', 
                component: Instances,
                meta: { requiresAuth: true, rule: "ADMIN" },
            },
            {
                path: 'admin/departments', 
                name: 'Department', 
                component: Department,
                meta: { requiresAuth: true, rule: "ADMIN" },
            },
            { 
                path: 'admin/road-maps', 
                name: 'RoadMap', 
                component: RoadMap,
                meta: { requiresAuth: true, rule: "ADMIN" }, 
            },
            { 
                path: 'orders', 
                name: 'Orders', 
                component: Orders,
                meta: { requiresAuth: true }
            },
            { 
                path: 'order-detail', 
                name: 'OrderDetail', 
                component: OrderDetail,
                meta: { requiresAuth: true }
            }
        ]
    }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_API_URL),
  routes,
});

// 🔒 Navigation Guard: Token va Role tekshirish
router.beforeEach((to, from, next) => {
    const token = Cookies.get("token");
    const user = Cookies.get("user") ? JSON.parse(Cookies.get("user")) : null;
  

    // 1. Login bo‘lish shart bo‘lgan sahifalar uchun
    if (to.meta.requiresAuth) {
      if (!token) {
        return next("/login"); // Token yo‘q bo‘lsa, login sahifasiga yo‘naltirish
      }
  
      // 2. ADMIN bo‘lishi shart bo‘lgan sahifalar uchun
      if (to.meta.rule && user?.rule !== to.meta.rule) {
        return next("/orders"); // Agar ADMIN bo‘lmasa, Orders sahifasiga yo‘naltirish
      }
    }
  
    next(); // Sahifaga ruxsat berish
});

export default router;
