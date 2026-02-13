import { useRoute } from 'vue-router';

export function useActiveUrl() {
    const route = useRoute();

    function urlIsActive(href: string) {
        return route.path === href || route.path.startsWith(href + '/');
    }

    return {
        urlIsActive,
    };
}
