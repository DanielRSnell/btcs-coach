import { ImgHTMLAttributes } from 'react';

export default function AppLogoIcon(props: ImgHTMLAttributes<HTMLImageElement>) {
    return (
        <img 
            {...props} 
            src="/HGF_Innovation_Logo_Registered.png" 
            alt="H.G. Fenton Company - Trust, Service and Innovation Since 1906"
        />
    );
}
