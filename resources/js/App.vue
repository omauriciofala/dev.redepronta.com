<template>
  <LoginView v-if="currentView === 'login'" />
  <AppLayout v-else>
    <UsersView v-if="currentView === 'users'" />
    <DeveloperView v-else-if="currentView === 'developer'" />
    <DesignSystemView v-else-if="currentView === 'design-system'" />
    <ChangelogView v-else-if="currentView === 'changelog'" />
    <IntegrationsView v-else-if="currentView === 'integrations'" />
    <SuppliesView v-else-if="currentView === 'supplies'" />
    <PeopleView v-else />
  </AppLayout>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import AppLayout from './components/layout/AppLayout.vue';
import PeopleView from './views/PeopleView.vue';
import SuppliesView from './views/SuppliesView.vue';
import UsersView from './views/UsersView.vue';
import LoginView from './views/LoginView.vue';
import DeveloperView from './views/DeveloperView.vue';
import DesignSystemView from './views/DesignSystemView.vue';
import ChangelogView from './views/ChangelogView.vue';
import IntegrationsView from './views/IntegrationsView.vue';
import { useNavigation } from './composables/useNavigation';
import { useAuth } from './composables/useAuth';

const { currentView } = useNavigation();
const { fetchCurrentUser } = useAuth();

onMounted(() => {
  fetchCurrentUser();
});
</script>
