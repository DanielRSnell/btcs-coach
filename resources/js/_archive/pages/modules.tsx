import { Head, Link } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { BookOpen, Target, Users } from "lucide-react";

interface Module {
    id: number;
    title: string;
    description: string;
    slug: string;
    type: 'coaching' | 'training' | 'assessment';
    topics: string[];
    learning_objectives: string;
    estimated_duration: number;
    difficulty: 'beginner' | 'intermediate' | 'advanced';
    is_active: boolean;
    sort_order: number;
    users_count?: number;
}

interface ModulesPageProps {
    modules: Module[];
}

const typeColors = {
    coaching: 'bg-green-100 text-green-800 hover:bg-green-200',
    training: 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200',
    assessment: 'bg-blue-100 text-blue-800 hover:bg-blue-200',
};

const difficultyColors = {
    beginner: 'bg-emerald-100 text-emerald-800',
    intermediate: 'bg-amber-100 text-amber-800',
    advanced: 'bg-red-100 text-red-800',
};

export default function Modules({ modules }: ModulesPageProps) {
    return (
        <AppLayout>
            <Head title="Coaching Modules" />
            
            <div className="space-y-6">
                <div>
                    <h1 className="text-3xl font-bold text-gray-900">Coaching Modules</h1>
                    <p className="text-gray-600 mt-2">
                        Explore our comprehensive coaching and training modules designed to accelerate your professional development.
                    </p>
                </div>

                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {modules.map((module) => (
                        <Card key={module.id} className="flex flex-col h-full hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="flex items-start justify-between mb-2">
                                    <Badge className={typeColors[module.type]}>
                                        {module.type}
                                    </Badge>
                                    <Badge variant="outline" className={difficultyColors[module.difficulty]}>
                                        {module.difficulty}
                                    </Badge>
                                </div>
                                <CardTitle className="text-xl">{module.title}</CardTitle>
                                <CardDescription className="text-sm leading-relaxed">
                                    {module.description}
                                </CardDescription>
                            </CardHeader>
                            
                            <CardContent className="flex-1 space-y-4">
                                {module.topics && module.topics.length > 0 && (
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <BookOpen className="h-4 w-4 text-gray-500" />
                                            <span className="text-sm font-medium text-gray-700">Topics Covered:</span>
                                        </div>
                                        <div className="flex flex-wrap gap-1">
                                            {module.topics.slice(0, 3).map((topic, index) => (
                                                <Badge key={index} variant="secondary" className="text-xs">
                                                    {topic}
                                                </Badge>
                                            ))}
                                            {module.topics.length > 3 && (
                                                <Badge variant="secondary" className="text-xs">
                                                    +{module.topics.length - 3} more
                                                </Badge>
                                            )}
                                        </div>
                                    </div>
                                )}

                                {module.learning_objectives && (
                                    <div>
                                        <div className="flex items-center gap-2 mb-2">
                                            <Target className="h-4 w-4 text-gray-500" />
                                            <span className="text-sm font-medium text-gray-700">Learning Objectives:</span>
                                        </div>
                                        <p className="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                            {module.learning_objectives}
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                            
                            <CardFooter className="pt-4 border-t">
                                <div className="flex items-center justify-between w-full">
                                    <div className="flex items-center gap-1 text-sm text-gray-500">
                                        <Users className="h-4 w-4" />
                                        <span>{module.users_count || 0} enrolled</span>
                                    </div>
                                    <Link href={`/modules/${module.slug}/chat`}>
                                        <Button size="sm">
                                            Start Session
                                        </Button>
                                    </Link>
                                </div>
                            </CardFooter>
                        </Card>
                    ))}
                </div>

                {modules.length === 0 && (
                    <div className="text-center py-12">
                        <BookOpen className="mx-auto h-12 w-12 text-gray-400" />
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No modules available</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            Check back later for new coaching modules.
                        </p>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}