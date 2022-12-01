<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';

defineProps({
  deals: Array
})
</script>

<template>
  <AppLayout title="Profile">
    <template #header>
      <div class="flex justify-center">
        <h2 class="text-gray-800 leading-tight dark:text-white">
          Deals 🤝
        </h2>
      </div>
    </template>

    <div class="p-6 max-w-7xl mx-auto">
      <div class="grid gap-6 mb-8">
        <div v-for="deal in deals"
            class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
        >
          <div class="p-4 flex justify-between">
              <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                🎉 {{ deal.readable_time }}
              </span>

              <span class="text-sm font-semibold text-gray-700 dark:text-green-200">
                <span v-if="parseInt(deal.net_profit) > 0">+</span>{{ deal.net_profit }} 💰
              </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>