<template>
  <AppLayout>
    <DeveloperView v-if="currentView === 'developer'" />
    <DesignSystemView v-else-if="currentView === 'design-system'" />
    <ChangelogView v-else-if="currentView === 'changelog'" />
    <IntegrationsView v-else-if="currentView === 'integrations'" />
    <PeopleView v-else />
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from './components/layout/AppLayout.vue';
import PeopleView from './views/PeopleView.vue';
import DeveloperView from './views/DeveloperView.vue';
import DesignSystemView from './views/DesignSystemView.vue';
import ChangelogView from './views/ChangelogView.vue';
import IntegrationsView from './views/IntegrationsView.vue';
import { useNavigation } from './composables/useNavigation';

const { currentView } = useNavigation();
</script>
