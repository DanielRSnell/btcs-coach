import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { 
    Target, 
    BookOpen, 
    CheckCircle, 
    Clock,
    TrendingUp
} from 'lucide-react';

interface DashboardProps {
    user: {
        id: number;
        name: string;
        email: string;
        role: 'admin' | 'member';
    };
    stats: {
        totalModules: number;
        completedModules: number;
        inProgressModules: number;
        pendingActionItems: number;
        completedActionItems: number;
        moduleCompletionRate: number;
    };
    pendingActionItems: Array<{
        id: number;
        title: string;
        priority: 'low' | 'medium' | 'high';
        due_date: string;
        status: string;
        module?: {
            id: number;
            title: string;
            slug: string;
        };
    }>;
    availableModules: Array<{
        id: number;
        title: string;
        description: string;
        slug: string;
        type: string;
        difficulty: string;
        estimated_duration: number;
    }>;
}

const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
        opacity: 1,
        transition: {
            staggerChildren: 0.1
        }
    }
};

const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0 }
};

export default function Dashboard({ user, stats, pendingActionItems, availableModules }: DashboardProps) {
    const getPriorityColor = (priority: string) => {
        switch (priority) {
            case 'high': return 'bg-red-100 text-red-800 border-red-200';
            case 'medium': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
            case 'low': return 'bg-green-100 text-green-800 border-green-200';
            default: return 'bg-gray-100 text-gray-800 border-gray-200';
        }
    };

    const getDifficultyColor = (difficulty: string) => {
        switch (difficulty) {
            case 'advanced': return 'bg-red-100 text-red-800';
            case 'intermediate': return 'bg-yellow-100 text-yellow-800';
            case 'beginner': return 'bg-green-100 text-green-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };


    return (
        <AppLayout
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-2xl font-semibold text-gray-900">
                            Welcome back, {user.name}!
                        </h2>
                        <p className="text-gray-600 mt-1">Ready to advance your PI and Situational Leadership skills?</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() => {
                                console.log('🧪 Test button clicked - calling window.profile.chart.show() without parameters');
                                if (window.profile?.chart?.show) {
                                    // Test calling show() without parameters - should use current user
                                    window.profile.chart.show();
                                } else {
                                    console.error('❌ window.profile.chart.show not available');
                                }
                            }}
                        >
                            View PI Chart
                        </Button>
                        <Badge variant={user.role === 'admin' ? 'default' : 'secondary'} className="text-sm">
                            {user.role === 'admin' ? 'Administrator' : 'Member'}
                        </Badge>
                    </div>
                </div>
            }
        >
            <Head title="Dashboard" />

            <motion.div 
                className="space-y-8"
                variants={containerVariants}
                initial="hidden"
                animate="visible"
            >
                {/* Stats Overview */}
                <motion.div variants={itemVariants}>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Total Modules</CardTitle>
                                <BookOpen className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.totalModules}</div>
                                <p className="text-xs text-muted-foreground">
                                    Assigned to you
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Completed</CardTitle>
                                <CheckCircle className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.completedModules}</div>
                                <p className="text-xs text-muted-foreground">
                                    Modules finished
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">In Progress</CardTitle>
                                <Clock className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.inProgressModules}</div>
                                <p className="text-xs text-muted-foreground">
                                    Currently working on
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Progress</CardTitle>
                                <TrendingUp className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.moduleCompletionRate}%</div>
                                <Progress value={stats.moduleCompletionRate} className="mt-2" />
                            </CardContent>
                        </Card>
                    </div>
                </motion.div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Available Modules */}
                    <motion.div variants={itemVariants}>
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <BookOpen className="h-5 w-5" />
                                    Available Modules
                                </CardTitle>
                                <CardDescription>
                                    Training modules assigned to you
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {availableModules.length > 0 ? (
                                    availableModules.map((module) => (
                                        <motion.div
                                            key={module.id}
                                            className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                                            whileHover={{ scale: 1.02 }}
                                            whileTap={{ scale: 0.98 }}
                                        >
                                            <div className="flex-1">
                                                <h4 className="font-medium">{module.title}</h4>
                                                <p className="text-sm text-gray-600 mt-1">
                                                    {module.description?.substring(0, 100)}...
                                                </p>
                                                <div className="flex items-center gap-2 mt-2">
                                                    <Badge variant="outline" className={getDifficultyColor(module.difficulty)}>
                                                        {module.difficulty}
                                                    </Badge>
                                                    <span className="text-xs text-gray-500">
                                                        {module.estimated_duration} min
                                                    </span>
                                                </div>
                                            </div>
                                            <Link href={`/modules/${module.slug}/chat`}>
                                                <Button size="sm">Start Session</Button>
                                            </Link>
                                        </motion.div>
                                    ))
                                ) : (
                                    <div className="text-center py-8 text-gray-500">
                                        <BookOpen className="h-12 w-12 mx-auto mb-3 opacity-50" />
                                        <p>No modules assigned yet</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </motion.div>

                    {/* Pending Action Items */}
                    <motion.div variants={itemVariants}>
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Clock className="h-5 w-5" />
                                    Pending Actions
                                </CardTitle>
                                <CardDescription>
                                    Items that need your attention
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {pendingActionItems.length > 0 ? (
                                    pendingActionItems.map((item) => (
                                        <motion.div
                                            key={item.id}
                                            className="flex items-center justify-between p-3 border rounded-lg"
                                            whileHover={{ backgroundColor: '#f9fafb' }}
                                        >
                                            <div className="flex-1">
                                                <h4 className="font-medium text-sm">{item.title}</h4>
                                                {item.module && (
                                                    <p className="text-xs text-blue-600 mt-1">
                                                        📘 {item.module.title}
                                                    </p>
                                                )}
                                                <div className="flex items-center gap-2 mt-1">
                                                    <Badge size="sm" className={getPriorityColor(item.priority)}>
                                                        {item.priority}
                                                    </Badge>
                                                    {item.due_date && (
                                                        <span className="text-xs text-gray-500">
                                                            Due: {new Date(item.due_date).toLocaleDateString()}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                            <Button size="sm" variant="outline">
                                                <CheckCircle className="h-4 w-4" />
                                            </Button>
                                        </motion.div>
                                    ))
                                ) : (
                                    <div className="text-center py-8 text-gray-500">
                                        <CheckCircle className="h-12 w-12 mx-auto mb-3 opacity-50" />
                                        <p>All caught up!</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </motion.div>
                </div>

            </motion.div>
        </AppLayout>
    );
}