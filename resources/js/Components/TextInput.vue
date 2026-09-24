<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="rounded-lg border-stone-300 bg-white px-3.5 py-3 shadow-sm focus:border-[#174c43] focus:ring-[#174c43]"
        v-model="model"
        ref="input"
    />
</template>
