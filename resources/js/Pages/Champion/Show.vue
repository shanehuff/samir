<template>
    <div class="champion-details flex h-screen" :style="{ backgroundImage: 'url(' + background + ')', backgroundPosition: 'top center', backgroundSize: 'cover', backgroundRepeat: 'no-repeat' }">
        <!-- Display stats on the left -->
        <div class="stats left flex-1 p-4 bg-gray-200 bg-opacity-50 text-sm">
            <ul>
                <li class="flex justify-between">
                    <span class="inline-block bg-purple-800 text-white px-4 py-2 rounded-full shadow py-2">👤</span>
                    <span class="inline-block bg-purple-800 text-white px-4 py-2 rounded-full shadow">{{ champion.archetype }}</span>
                </li>
                <li class="flex justify-between mt-4">
                    <span class="inline-block bg-green-600 text-white px-4 py-2 rounded-full shadow py-2">💼</span>
                    <span class="inline-block bg-green-600 text-white px-4 py-2 rounded-full shadow">${{ champion.current_capital }}</span>
                </li>
                <li class="flex justify-between mt-4">
                    <span class="inline-block bg-gray-700 text-white px-4 py-2 rounded-full shadow py-2">🎂</span>
                    <span class="flex items-center whitespace-nowrap text-xs md:text-sm inline-block bg-gray-700 text-white px-4 py-2 rounded-full shadow">{{ champion.age }}</span>
                </li>

                <li class="flex justify-between mt-4">
                    <span class="inline-block bg-teal-600 text-white px-4 py-2 rounded-full shadow py-2">📈</span>
                    <span class="flex items-center whitespace-nowrap text-xs md:text-sm inline-block bg-teal-600 text-white px-4 py-2 rounded-full shadow">{{ champion.apy }}%</span>
                </li>
            </ul>
        </div>

        <!-- Display character name in the middle -->
        <div class="name flex-1 p-4 text-center relative">
            <div class="absolute inset-0 bg-black opacity-40"></div>
            <h1 class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded-full shadow text-2xl font-bold absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">{{ champion.name }}</h1>
            <!-- Add other character details as needed -->
        </div>

        <!-- Display more stats on the right -->
        <div class="stats right flex-1 p-4 bg-gray-300 bg-opacity-50 text-sm">
            <ul>
                <li class="mt-2 mb-4">
                    <HealthPointBar :remaining-h-p="champion.remain_capital" :full-h-p="champion.capital"></HealthPointBar>
                </li>

                <li class="flex justify-between mt-4">
                    <span class="inline-block bg-indigo-800 text-white px-4 py-2 rounded-full shadow py-2">💪</span>
                    <span class="inline-block bg-indigo-800 text-white px-4 py-2 rounded-full shadow">{{ champion.grind }}</span>
                </li>

                <li class="flex justify-between mt-4">
                    <span class="py-2 inline-block bg-amber-800 text-white px-4 py-2 rounded-full shadow">🏺</span>
                    <span class="inline-block bg-amber-800 text-white px-4 py-2 rounded-full shadow">${{ champion.profit }}</span>
                </li>

                <li class="flex justify-between mt-4">
                    <span class="py-2 inline-block bg-green-500 text-white px-4 py-2 rounded-full shadow">💰</span>
                    <span class="inline-block bg-green-500 text-white px-4 py-2 rounded-full shadow">${{ champion.income }}</span>
                </li>

                <li class="flex justify-between mt-4">
                    <span class="py-2 inline-block bg-blue-500 text-white px-4 py-2 rounded-full shadow">💸</span>
                    <span class="inline-block bg-blue-500 text-white px-4 py-2 rounded-full shadow">${{ champion.fee }}</span>
                </li>

                <li class="flex justify-between mt-4">
                    <span class="py-2 inline-block bg-lime-500 text-white px-4 py-2 rounded-full shadow">🚪</span>
                    <span class="inline-block bg-lime-500 text-white px-4 py-2 rounded-full shadow">${{ champion.entry }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import HealthPointBar from "./Partials/HealthPointBar.vue";
export default {
    components: {
        HealthPointBar
    },

    props: {
        champion: {
            type: Object,
            required: true,
        },
        background: {
            type: String,
            required: true,
        },
    },
};
</script>
