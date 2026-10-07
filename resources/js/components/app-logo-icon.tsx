import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 3h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-8l-5 4v-4H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm4.5 4.5a2 2 0 0 0-2 2V12h3V9.5a1 1 0 0 0-1-1Zm7 0a2 2 0 0 0-2 2V12h3V9.5a1 1 0 0 0-1-1Z" />
        </svg>
    );
}
