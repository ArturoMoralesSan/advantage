<template>
    <form>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Datos de la venta
            </h3>

            <div class="md:row mb-2">
                <div class="col">
                    <div class="form-control">
                        <label for="user_id">Cliente</label>
                        <select-field name="user_id" v-model="fields.user_id" :options="users" :initial="sale.user_id || '' ">
                        </select-field>
                        <field-errors name="user_id"></field-errors>
                    </div>
                </div>
            </div>

            
            <!-- <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="status">Estado</label>
                        <select-field name="status" v-model="fields.status" :options="status" :initial="sale.status || '' ">
                        </select-field>
                        <field-errors name="status"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="is_paid">Pagado</label>
                        <select-field name="is_paid" v-model="fields.is_paid" :options="paid" :initial="sale.is_paid || '' ">
                        </select-field>
                        <field-errors name="is_paid"></field-errors>
                    </div>
                </div>
            </div> -->
        </section>

        <!-- Sección de Costo -->
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Costo
            </h3>
            <div class="md:row mb-2">
                <div class="col">
                    <div class="form-control">
                        <label for="product_name">Producto</label>
                        <text-field name="product_name" v-model="fields.product_name" maxlength="80" :initial="sale.product_name || '' ">
                        </text-field>
                        <field-errors name="product_name"></field-errors>
                    </div>
                </div>
            </div>

            <div class="md:row mb-2">
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="type_id">Tipo de impresión</label>
                        <select-field name="type_id" v-model="fields.type_id" :options="types" :initial="sale?.product?.type_id || '' ">
                        </select-field>
                        <field-errors name="type_id"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="product_id">Material</label>
                        <select-field name="product_id" v-model="fields.product_id" :options="filteredProducts" :initial="sale.product_id || '' ">
                        </select-field>
                        <field-errors name="product_id"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="width">Acabado / Corte</label>
                        <select-field name="cut_id" v-model="fields.cut_id" :options="cuts" :initial="sale.cut_id || '' ">
                        </select-field>
                        <field-errors name="width"></field-errors>
                    </div>
                </div>
            </div>

            <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="width">Ancho</label>
                        <text-field name="width" v-model="fields.width" maxlength="80" :initial="sale.width || '' ">
                        </text-field>
                        <field-errors name="width"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="height">Largo</label>
                        <text-field name="height" v-model="fields.height" maxlength="80" :initial="sale.height || '' ">
                        </text-field>
                        <field-errors name="height"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="base_price">Costo por unidad</label>
                        <text-field disabled name="base_price" v-model="fields.base_price" maxlength="80" :initial="sale.base_price || '' ">
                        </text-field>
                        <field-errors name="base_price"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="profit_percentage">Utilidad por unidad</label>
                        <text-field name="profit_percentage" v-model="fields.profit_percentage" maxlength="6" :initial="sale.profit_percentage || '' ">
                        </text-field>
                        <field-errors name="profit_percentage"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col">
                    <div class="sale-price-wrapper">
                        <div class="sale-price-container">
                            <label class="sale-price-label">Precio de venta</label>
                            <p class="sale-price-value">${{ salePrice.toFixed(2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <div class="text-center">
            <button class="btn" :class="Object.keys(sale).length > 0 ? 'btn--blue--dashboard' : 'btn--success'">
                {{ Object.keys(sale).length > 0 ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</template>

<script>
import BaseForm from '../../main/components/forms/base/BaseForm.vue';

export default {
    extends: BaseForm,
    props: {
        users: {
            required: true,
            type: Object
        },
        cuts: {
            required: true,
            type: Object
        },
        status: {
            required: true,
            type: Object
        },
        types: {
            required: true,
            type: Object
        },
        paid: {
            required: true,
            type: Object
        },
        sale: {
            required: true,
            type: [Array, Object]
        },
        products: {
            required: true,
            type: Array
        },
        
    },

    data() {
        return {
            formattedProducts: {}
        };
    },

    created() {
        this.formatProducts();
    },

    watch: {
        'fields.product_id'(newProductId) {
            const selectedProduct = this.products.find(product => product.id == newProductId);
            if (selectedProduct) {
                this.fields.base_price = selectedProduct.costo_venta || 0;
            }
        }
    },


    computed: {
        filteredProducts() {
            if (!this.fields.type_id) return this.formattedProducts;
            
            let filtered = {};
            this.products.forEach(product => {
                if (product.type_id == this.fields.type_id) {
                    filtered[product.id] = product.name;
                }
            });
            return filtered;
        },

        salePrice() {
            const basePrice = parseFloat(this.fields.base_price) || 0;
            const profitPercentage = parseFloat(this.fields.profit_percentage) || 0;
            return basePrice + (basePrice * profitPercentage / 100);
        }
    },

    methods: {
        formatProducts() {
            this.formattedProducts = this.products.reduce((obj, product) => {
                obj[product.id] = product.name;
                return obj;
            }, {});
        }
    }
};
</script>

<style scoped>
    .sale-price-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 1rem;
    }

    .sale-price-container {
        text-align: center; 
        padding: 1.5rem;
    }

    .sale-price-label {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sale-price-value {
        font-size: 24px;
        font-weight: bold;
        color: #2c7a7b; /* Verde azulado */
    }

</style>
