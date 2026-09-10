<template>
  <MovementModal
    :is-open="isOpen"
    :depots="depots"
    :materials="materials"
    :initial-material="initialMaterial"
    :initial-depot="initialDepot"
    :initial-type="'TRANSFER'"
    @close="$emit('close')"
    @saved="$emit('transferred')"
    @transferred="$emit('transferred')"
  />
</template>

<script setup lang="ts">
import MovementModal from '@/components/supplies/MovementModal.vue';

defineProps<{
  isOpen: boolean;
  depots: any[];
  materials: any[];
  initialMaterial?: any;
  initialDepot?: any;
}>();

defineEmits<{
  (e: 'close'): void;
  (e: 'transferred'): void;
}>();
</script>
