import type { ImgHTMLAttributes } from 'react';

export default function AppLogoIcon(props: ImgHTMLAttributes<HTMLImageElement>) {
    return (
        <img
            {...props}
            src="/Images/logo.png"
            alt="Z-Animex"
            className={props.className ?? "h-8 w-auto"}
        />
    );
}
