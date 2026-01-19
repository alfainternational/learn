import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        isAuthenticated: false,
        loading: false,
    }),

    getters: {
        isAdmin: (state) => state.user?.role === 'admin',
        isAdvertiser: (state) => state.user?.role === 'advertiser',
        subscriptionTier: (state) => state.user?.subscription_tier || 'free',
        aiCreditsRemaining: (state) => state.user?.ai_credits_remaining || 0,
        hasActiveSubscription: (state) => {
            if (!state.user) return false;
            return state.user.subscription_tier !== 'free' &&
                state.user.subscription_expires_at &&
                new Date(state.user.subscription_expires_at) > new Date();
        },
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            try {
                const response = await axios.post('/login', credentials);
                this.token = response.data.data.token;
                this.user = response.data.data.user;
                this.isAuthenticated = true;

                localStorage.setItem('token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

                return response.data;
            } catch (error) {
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async register(data) {
            this.loading = true;
            try {
                const response = await axios.post('/register', data);
                this.token = response.data.data.token;
                this.user = response.data.data.user;
                this.isAuthenticated = true;

                localStorage.setItem('token', this.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

                return response.data;
            } catch (error) {
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await axios.post('/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.user = null;
                this.token = null;
                this.isAuthenticated = false;

                localStorage.removeItem('token');
                delete axios.defaults.headers.common['Authorization'];
            }
        },

        async fetchUser() {
            if (!this.token) return;

            try {
                const response = await axios.get('/me');
                this.user = response.data.data.user;
                this.isAuthenticated = true;
            } catch (error) {
                // Token is invalid
                this.logout();
            }
        },

        async updateProfile(data) {
            try {
                const response = await axios.put('/me', data);
                this.user = response.data.data.user;
                return response.data;
            } catch (error) {
                throw error;
            }
        },
    },
});
