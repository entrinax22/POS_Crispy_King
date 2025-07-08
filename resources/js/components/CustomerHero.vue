<template>
    <section
        class="mb-8 flex flex-wrap items-center justify-between rounded-2xl bg-gradient-to-r from-yellow-50 via-orange-50 to-yellow-100 p-6 shadow md:p-10"
    >
        <div class="min-w-[250px] flex-1 p-2 md:min-w-[320px] md:p-4">
            <h1 class="mb-2 text-3xl font-extrabold text-orange-800 drop-shadow md:text-4xl">Welcome to Crispy King!</h1>
            <p class="mb-6 text-lg text-gray-700 md:text-xl">
                Delicious fried chicken and more, freshly made for you. Order online and enjoy the taste of royalty!
            </p>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button
                    @click="$emit('order-now')"
                    class="order-btn rounded-full bg-orange-600 px-6 py-3 text-lg font-semibold text-white shadow transition-colors duration-200 hover:bg-orange-700 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                >
                    Order Now
                </button>
            </div>
        </div>
        <img src="/images/chicken.jpeg" alt="Crispy Chicken" class="m-4 max-w-xs flex-1 rounded-2xl shadow-lg md:max-w-md" />
    </section>
</template>

<script>
export default {
    name: 'CustomerHero',
};
</script>
