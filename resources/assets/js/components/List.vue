<template>
    <div>
        <table class="table-inner">
            <tr>
                <td :colspan="filterz && filterz.length > 0 ? 1 : 2">
                    <div class="has-feedback">
                        <input type="text" class="form-control" style="padding-left: 32px;" placeholder="Search..." v-model="search"/>
                        <i class="glyphicon glyphicon-search form-control-feedback" style="left: 0;"></i>
                    </div>
                </td>
                <td v-if="filterz && filterz.length > 0">
                    <div class="dropdown" id="filter">
                        <button class="btn btn-default" style="width: 100%" @click="window.$('#filter').toggleClass('open')">
                            <span class="glyphicon glyphicon-filter"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" style="padding: 0 20px 20px 20px; width: 350px;">
                            <div v-for="filter in filterz">
                                <li class="title">{{ filter.title }}</li>
                                <li><component :is="filter.component" v-on:input="v => {filter.value = v; elements.splice()}" v-bind="filter.props"></component></li>
                            </div>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <div class="panel panel-default" style="overflow: auto;">
            <table class="table">
                <thead>
                <tr>
                    <th v-if="icon"></th>
                    <th v-for="field in fields">{{ typeof field === 'string' ? field : '' }}</th>
                </tr>
                </thead>
                <tbody>
                <tr class="object-list-item" v-for="(element, index) in filtered" :class="{selected: value === element}" @click="$emit('input', element)">
                    <td v-if="icon"><img :src="getIcon(element)" style="width: 24px;"/></td>
                    <td v-for="field in fields" v-html="getFieldText(element, field)"></td>
                </tr>
                </tbody>
            </table>
        </div>

        <span class="list-info pull-right">total: <b>{{ elements.length }}</b>, filtered: <b>{{ filtered.length }}</b></span>
    </div>
</template>

<script>
    export default {
        props: ["value", "elements", "fields", "icon", "filters"],

        data() {
            return {
                search: "",
                filterz: this.filters,
                window: window
            };
        },

        computed: {
            filtered: function () {
                return this.elements.filter((element) => {
                    for (let key in this.filterz) {
                        if (!this.filterz[key].filter(element, this.filterz[key].value)) {
                            return false;
                        }
                    }

                    let match = false;

                    for (let field in this.fields) {
                        if (typeof this.fields[field] === 'function') {
                            continue;
                        }

                        let property = this.window.getPropertyByPath(element, this.fields[field]);
                        if (("" + property).toString().toLowerCase().match(this.search.toLowerCase())) {
                            match = true;
                            break;
                        }
                    }

                    return match;
                });
            }
        },

        methods: {
            getIcon: function (element) {
                if (this.icon === undefined) {
                    return "";
                }

                let property = this.window.getPropertyByPath(element, this.icon);
                return window.library.icons[property] ? this.window.library.icons[property].url : "";
            },
            getFieldText: function (element, field) {
                if (typeof field === "function") {
                    return field(element);
                }

                return window.getPropertyByPath(element, field)
            }
        }
    };
</script>

<style scoped>

</style>
