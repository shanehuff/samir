<!--suppress JSUnresolvedReference -->
<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';

defineProps({
    profit: Object,
    vnd: Number,
    roi: String
})

const formatToVND = number => {
    return number.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white text-center flex justify-between">
                <a :href="route('profits.show', (profit.id - 1))" class="text-sm text-gray-700 underline">&lt; bacK</a>
                <span>Profit #{{ profit.id }}</span>
                <a :href="route('profits.show', (profit.id + 1))" class="text-sm text-gray-700 underline">Next &gt;</a>
            </h2>

            <div class="flex justify-center">

            </div>
        </template>

        <div class="p-6 max-w-7xl mx-auto">
            <div class="gap-6 mb-8">
                <div
                        class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            Buy <span class="text-green-500">{{ formatToVND(profit.buy_price * profit.trade.qty * vnd) }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            Sell <span class="text-red-500">{{ formatToVND(profit.sell_price * profit.trade.qty * vnd) }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            Duration <span class="text-white">{{ profit.duration_readable }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            Profit <span class="text-green-500">{{ formatToVND(profit.realized_profit * vnd) }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            Fee <span class="text-red-500">{{ formatToVND(profit.fee * vnd) }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            Net Profit <span class="text-green-500">{{ formatToVND(profit.net_profit * vnd) }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="mb-4 min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <p class="flex justify-between text-md font-semibold text-gray-700 dark:text-gray-200">
                            ROI <span class="text-green-500">{{ roi }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>