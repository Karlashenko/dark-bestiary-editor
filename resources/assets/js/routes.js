import VueRouter from "vue-router";

let routes = [
    {
        path: "/",
        redirect: "/units"
    },
    {
        path: "/units/:id?",
        component: require("./components/Units.vue"),
        props: true
    },
    {
        path: "/unit_groups/:id?",
        component: require("./components/UnitGroups.vue"),
        props: true
    },
    {
        path: "/archetypes/:id?",
        component: require("./components/Archetypes.vue"),
        props: true
    },
    {
        path: "/backgrounds/:id?",
        component: require("./components/Backgrounds.vue"),
        props: true
    },
    {
        path: "/dialogues/:id?",
        component: require("./components/Dialogues.vue"),
        props: true
    },
    {
        path: "/masteries/:id?",
        component: require("./components/Masteries.vue"),
        props: true
    },
    {
        path: "/food/:id?",
        component: require("./components/Food.vue"),
        props: true
    },
    {
        path: "/phrases/:id?",
        component: require("./components/Phrases.vue"),
        props: true
    },
    {
        path: "/relics/:id?",
        component: require("./components/Relics.vue"),
        props: true
    },
    {
        path: "/item_modifiers/:id?",
        component: require("./components/ItemModifiers.vue"),
        props: true
    },
    {
        path: "/skill_sets/:id?",
        component: require("./components/SkillSets.vue"),
        props: true
    },
    {
        path: "/weather/:id?",
        component: require("./components/Weather.vue"),
        props: true
    },
    {
        path: "/formulas/:id?",
        component: require("./components/Formulas.vue"),
        props: true
    },
    {
        path: "/skins/:id?",
        component: require("./components/Skins.vue"),
        props: true
    },
    {
        path: "/skills/:id?",
        component: require("./components/Skills.vue"),
        props: true
    },
    {
        path: "/skill_categories/:id?",
        component: require("./components/SkillCategories.vue"),
        props: true
    },
    {
        path: "/talent_categories/:id?",
        component: require("./components/TalentCategories.vue"),
        props: true
    },
    {
        path: "/behaviours/:id?",
        component: require("./components/Behaviours.vue"),
        props: true
    },
    {
        path: "/effects/:id?",
        component: require("./components/Effects.vue"),
        props: true
    },
    {
        path: "/validators/:id?",
        component: require("./components/Validators.vue"),
        props: true
    },
    {
        path: "/missiles/:id?",
        component: require("./components/Missiles.vue"),
        props: true
    },
    {
        path: "/items/:id?",
        component: require("./components/Items.vue"),
        props: true
    },
    {
        path: "/item_sets/:id?",
        component: require("./components/ItemSets.vue"),
        props: true
    },
    {
        path: "/item_types/:id?",
        component: require("./components/ItemTypes.vue"),
        props: true
    },
    {
        path: "/item_categories/:id?",
        component: require("./components/ItemCategories.vue"),
        props: true
    },
    {
        path: "/item_rarities/:id?",
        component: require("./components/ItemRarities.vue"),
        props: true
    },
    {
        path: "/recipes/:id?",
        component: require("./components/Recipes.vue"),
        props: true
    },
    {
        path: "/loot/:id?",
        component: require("./components/Loot.vue"),
        props: true
    },
    {
        path: "/talents/:id?",
        component: require("./components/Talents.vue"),
        props: true
    },
    {
        path: "/rewards/:id?",
        component: require("./components/Rewards.vue"),
        props: true
    },
    {
        path: "/scenarios/:id?",
        component: require("./components/Scenarios.vue"),
        props: true
    },
    {
        path: "/scenes/:id?",
        component: require("./components/Scenes.vue"),
        props: true
    },
    {
        path: "/achievements/:id?",
        component: require("./components/Achievements.vue"),
        props: true
    },
    {
        path: "/achievement_conditions/:id?",
        component: require("./components/AchievementConditions.vue"),
        props: true
    },
    {
        path: "/attributes/:id?",
        component: require("./components/Attributes.vue"),
        props: true
    },
    {
        path: "/properties/:id?",
        component: require("./components/Properties.vue"),
        props: true
    },
    {
        path: "/currencies/:id?",
        component: require("./components/Currencies.vue"),
        props: true
    },
    {
        path: "/environments/:id?",
        component: require("./components/Environments.vue"),
        props: true
    },
    {
        path: "/ai/:id?",
        component: require("./components/BehaviourTrees.vue"),
        props: true
    },
    {
        path: "/i18n/:id?",
        component: require("./components/I18N.vue"),
        props: true
    }
];

export default new VueRouter({
    linkExactActiveClass: "active",
    routes: routes
});
