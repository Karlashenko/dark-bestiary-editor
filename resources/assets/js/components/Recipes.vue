<template>
    <div>
        <div>
            <div v-if="loading" class="loading"></div>

            <div class="row" v-if="!loading">
                <div class="col-md-4">
                    <list :elements="recipes" :fields="['id', 'item.name_i18n.en', 'item.type.name_i18n.en', 'item.rarity.type', 'item.level', 'is_unlocked']" :icon="'item.icon'" v-model="selected" :filters="getFilters()"></list>
                </div>

                <div class="col-md-8">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Category</td>
                                    <td>
                                        <selectpicker v-model="selected.category" :options="window.library.recipeCategories"></selectpicker>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Unlocked</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_unlocked">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Custom</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="checkbox" v-model="selected.is_custom">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Item</td>
                                    <td>
                                        <item-field v-model="selected.item_id" :items="items"></item-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Blueprint</td>
                                    <td>
                                        <item-field v-model="selected.blueprint_id" :items="items"></item-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Count</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="number" class="form-control" v-model="selected.item_count">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Ingredients</td>
                                    <td>
                                        <table class="table-inner">
                                            <tr v-for="ingredient in selected.ingredients">
                                                <td>
                                                    <input type="number" class="form-control" v-model="ingredient.pivot.count">
                                                </td>

                                                <td style="width: 100%;">
                                                    <item-field v-model="ingredient.id" :items="items"></item-field>
                                                </td>

                                                <td>
                                                    <button class="btn btn-default" @click="removeIngredient(selected, ingredient)"><span class="glyphicon glyphicon-trash"></span></button>
                                                </td>
                                            </tr>

                                            <tr style="height: 50px;">
                                                <td colspan="2">
                                                    <button class="btn btn-default" style="width: 128px; height: 32px;" @click="addIngredient(selected)">
                                                        <span class="glyphicon glyphicon-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="panel-footer">
                            <div class="pull-left">
                                <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                                <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Events} from "./../events";

    export default {
        data() {
            return {
                loading: true,
                recipes: [],
                items: [],
                itemTypes: [],
                itemRarities: [],
                itemCategories: [],
                window: window,
                library: window.library,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);

            this.load();
        },

        methods: {
            load: function () {
                this.loading = true;

                window.axios.get("/frontend/recipes").then(response => {
                    this.recipes = response.data;
                    this.select(this.id === undefined ? this.recipes[0] : this.recipes.find((element) => element.id === this.id));
                }).then(() => {
                    window.axios.get("/frontend/items").then(response => {
                        this.items = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/item_types").then(response => {
                        this.itemTypes = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/item_rarities").then(response => {
                        this.itemRarities = response.data;
                    });
                }).then(() => {
                    window.axios.get("/frontend/item_categories").then(response => {
                        this.itemCategories = response.data;
                        this.loading = false;
                    });
                });
            },

            getFilters: function () {
                return [
                    {
                        title: "Category",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: window.library.recipeCategories,
                        },
                        filter: (element, value) => {
                            return !value || element.category === value
                        },
                        value: null
                    },
                    {
                        title: "Type",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.itemTypes,
                            valuecallback: (type) => type.id,
                            labelcallback: (type) => type.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.item.type_id === value
                        },
                        value: null
                    },
                    {
                        title: "Slot",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.library.itemSlotTypes,
                        },
                        filter: (element, value) => {
                            return !value || element.item.slot === value
                        },
                        value: null
                    },
                    {
                        title: "Rarity",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.itemRarities,
                            valuecallback: (rarity) => rarity.id,
                            labelcallback: (rarity) => rarity.name_i18n.en
                        },
                        filter: (element, value) => {
                            return !value || element.item.rarity_id === value
                        },
                        value: null
                    },
                    {
                        title: "Category",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: this.itemCategories,
                            labelcallback: (category) => category.name_i18n.en
                        },
                        filter: (element, category) => {
                            if (category === null) {
                                return true;
                            }

                            for (let key in category.types) {
                                if (category.types[key].id == element.item.type_id) {
                                    return true;
                                }
                            }

                            return false;
                        },
                        value: null
                    },
                    {
                        title: "Custom",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_custom == value;
                        },
                        value: null
                    },
                    {
                        title: "Unlocked",
                        component: "selectpicker",
                        props: {
                            nullable: true,
                            options: [true, false]
                        },
                        filter: (element, value) => {
                            return value == null || element.is_unlocked == value;
                        },
                        value: null
                    },
                    {
                        title: "Have Flags",
                        component: "flags",
                        props: {
                            flags: window.library.itemFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || element.item.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    },
                    {
                        title: "Don't have flags",
                        component: "flags",
                        props: {
                            flags: window.library.itemFlags
                        },
                        filter: (element, value) => {
                            return !value || value.length === 0 || !element.item.flags.some(flag => value.indexOf(flag) >= 0);
                        },
                        value: null
                    }
                ];
            },

            create: function () {
                this.recipes.push({
                    fresh: true,
                    ingredients: []
                });

                this.select(this.recipes[this.recipes.length - 1]);
            },

            select: function (recipe) {
                this.selected = recipe;
            },

            duplicate: function (recipe) {
                let copy = Object.assign({}, recipe);
                copy.fresh = true;
                this.recipes.push(copy);
                this.select(copy);
            },

            reset: function (recipe) {
                if (recipe.fresh) {
                    return;
                }

                let index = this.recipes.indexOf(recipe);

                window.axios.get("/frontend/recipes/" + recipe.id).then(response => {
                    this.recipes[index] = response.data;
                    this.select(this.recipes[index]);
                });
            },

            save: function (recipe) {
                let index = this.recipes.indexOf(recipe);

                if (recipe.fresh) {
                    window.axios.post("/frontend/recipes", recipe).then(response => {
                        this.recipes[index] = response.data;
                        this.select(this.recipes[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/recipes/" + recipe.id, recipe).then(response => {
                    this.recipes[index] = response.data;
                    this.select(this.recipes[index]);
                });
            },

            destroy: function (recipe) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.recipes.indexOf(recipe);

                if (recipe.fresh) {
                    this.recipes.splice(index, 1);
                    this.select(this.recipes[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/recipes/" + recipe.id).then(() => {
                    this.recipes.splice(index, 1);
                    this.select(this.recipes[index <= 0 ? 0 : index - 1]);
                });
            },

            addIngredient: function (recipe) {
                recipe.ingredients.push({
                    id: this.items[0] ? this.items[0].id : undefined,
                    pivot: {count: 1}
                });
            },

            removeIngredient: function (recipe, ingredient) {
                let index = recipe.ingredients.indexOf(ingredient);
                recipe.ingredients.splice(index, 1);
            },

            saveAll: function () {
                for (let key in this.recipes) {
                    let recipe = this.recipes[key];

                    if (recipe.fresh) {
                        window.axios.post("/frontend/recipes/", recipe).then(response => {
                            this.recipes[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/recipes/" + recipe.id, recipe).then(response => {
                        this.recipes[key] = response.data;
                    });
                }
            }
        }
    };
</script>
