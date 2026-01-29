import Button from "../global/button";

interface NavBodyProps {
    setVisible: (visible: boolean) => void;
}

export default function NavBody(props: NavBodyProps) {
    const handleClick = () => props.setVisible(false);

    return (
        <div className="p-4 flex flex-col gap-1">
            <Button
                stretch={true}
                selected={true}
                href="/"
                onClick={handleClick}
            >
                Home
            </Button>
            <Button
                stretch={true}
                selected={false}
                href="/about"
                onClick={handleClick}
            >
                About
            </Button>
            <Button
                stretch={true}
                selected={false}
                href="/projects"
                onClick={handleClick}
            >
                Projects
            </Button>
            <Button
                stretch={true}
                selected={false}
                href="/blog"
                onClick={handleClick}
            >
                Blog
            </Button>
            <Button
                stretch={true}
                selected={false}
                href="/contact"
                onClick={handleClick}
            >
                Contact
            </Button>
        </div>
    );
}
