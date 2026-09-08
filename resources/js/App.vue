<template>
  <AppLayout>
    <DesignSystemView v-if="currentView === 'design-system'" />
    <PeopleView v-else />
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from './components/layout/AppLayout.vue';
import PeopleView from './views/PeopleView.vue';
import DesignSystemView from './views/DesignSystemView.vue';
import { useNavigation } from './composables/useNavigation';

const { currentView } = useNavigation();
</script>
