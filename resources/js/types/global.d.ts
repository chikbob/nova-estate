import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { PageProps as AppPageProps } from './';

type RouteHelper = {
    (name: string, params?: unknown, absolute?: boolean): string;
    (): { current: (name: string) => boolean };
};

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    /* eslint-disable no-var */
    var route: RouteHelper;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: RouteHelper;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}
