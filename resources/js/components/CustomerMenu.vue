<template>
    <section id="menu" class="menu">
        <h2 class="mb-6 text-center text-2xl font-bold text-orange-700">Our Menu</h2>
        <ul class="menu-list grid gap-6 md:grid-cols-3">
            <li class="flex flex-col items-center rounded-xl bg-white p-4 shadow-md">
                <img
                    src="/images/chicken.jpeg"
                    alt="Signature Fried Chicken"
                    class="mb-3 h-24 w-24 rounded-full border-4 border-orange-100 object-cover shadow"
                />
                <h3 class="mb-1 text-lg font-bold text-orange-700">Signature Fried Chicken</h3>
                <p class="text-center text-gray-600">Golden, crispy, and juicy. Served with your choice of sides.</p>
            </li>
            <li class="flex flex-col items-center rounded-xl bg-white p-4 shadow-md">
                <img
                    src="/images/chicken.jpeg"
                    alt="Chicken Burger"
                    class="mb-3 h-24 w-24 rounded-full border-4 border-orange-100 object-cover shadow"
                />
                <h3 class="mb-1 text-lg font-bold text-orange-700">Chicken Burger</h3>
                <p class="text-center text-gray-600">Crunchy chicken fillet, fresh veggies, and our special sauce.</p>
            </li>
            <li class="flex flex-col items-center rounded-xl bg-white p-4 shadow-md">
                <img
                    src="/images/chicken.jpeg"
                    alt="Family Meals"
                    class="mb-3 h-24 w-24 rounded-full border-4 border-orange-100 object-cover shadow"
                />
                <h3 class="mb-1 text-lg font-bold text-orange-700">Family Meals</h3>
                <p class="text-center text-gray-600">Perfect for sharing! Includes chicken, fries, and drinks.</p>
            </li>
        </ul>
    </section>
</template>

<script>
export default {
    name: 'CustomerMenu',
};
</script>

<style scoped>
.menu {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
@media (max-width: 600px) {
    .menu {
        padding: 1rem;
    }
    .menu-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
}
</style>
