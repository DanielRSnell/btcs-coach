import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import AppLayout from '@/layouts/app-layout';
import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { 
    Trophy, 
    Target, 
    BookOpen, 
    Calendar, 
    CheckCircle, 
    Clock,
    Star,
    TrendingUp,
    Users,
    Award
} from 'lucide-react';

interface DashboardProps {
    user: {
        id: number;
        name: string;
        email: string;
        role: 'admin' | 'member';
    };
    stats: {
        totalSessions: number;
        completedSessions: number;
        totalAchievements: number;
        totalPoints: number;
        pendingActionItems: number;
        completedActionItems: number;
        assignedModules: number;
    };
    recentSessions: Array<{
        id: number;
        topic: string;
        status: string;
        duration: number;
        started_at: string;
        module?: {
            title: string;
        };
    }>;
    pendingActionItems: Array<{
        id: number;
        title: string;
        priority: 'low' | 'medium' | 'high';
        due_date: string;
        status: string;
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
    recentAchievements: Array<{
        id: number;
        title: string;
        description: string;
        points: number;
        badge_color: string;
        unlocked_at: string;
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

export default function Dashboard({ user, stats, recentSessions, pendingActionItems, availableModules, recentAchievements }: DashboardProps) {
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

    const completionRate = stats.totalSessions > 0 ? Math.round((stats.completedSessions / stats.totalSessions) * 100) : 0;

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
                    <Badge variant={user.role === 'admin' ? 'default' : 'secondary'} className="text-sm">
                        {user.role === 'admin' ? 'Administrator' : 'Member'}
                    </Badge>
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
                                <CardTitle className="text-sm font-medium">Total Sessions</CardTitle>
                                <Calendar className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.totalSessions}</div>
                                <p className="text-xs text-muted-foreground">
                                    {stats.completedSessions} completed
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Achievements</CardTitle>
                                <Trophy className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.totalAchievements}</div>
                                <p className="text-xs text-muted-foreground">
                                    {stats.totalPoints} total points
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Action Items</CardTitle>
                                <Target className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stats.pendingActionItems}</div>
                                <p className="text-xs text-muted-foreground">
                                    {stats.completedActionItems} completed
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">Progress</CardTitle>
                                <TrendingUp className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{completionRate}%</div>
                                <Progress value={completionRate} className="mt-2" />
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

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Recent Sessions */}
                    <motion.div variants={itemVariants}>
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Users className="h-5 w-5" />
                                    Recent Sessions
                                </CardTitle>
                                <CardDescription>
                                    Your latest coaching activities
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {recentSessions.length > 0 ? (
                                    recentSessions.map((session) => (
                                        <div key={session.id} className="flex items-center justify-between p-3 border rounded-lg">
                                            <div>
                                                <h4 className="font-medium text-sm">{session.topic || 'Coaching Session'}</h4>
                                                <p className="text-xs text-gray-500 mt-1">
                                                    {session.module?.title && `${session.module.title} • `}
                                                    {session.duration && `${session.duration} min • `}
                                                    {new Date(session.started_at).toLocaleDateString()}
                                                </p>
                                            </div>
                                            <Badge variant={session.status === 'completed' ? 'default' : 'secondary'}>
                                                {session.status}
                                            </Badge>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center py-8 text-gray-500">
                                        <Calendar className="h-12 w-12 mx-auto mb-3 opacity-50" />
                                        <p>No sessions yet</p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </motion.div>

                    {/* Recent Achievements */}
                    <motion.div variants={itemVariants}>
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Award className="h-5 w-5" />
                                    Recent Achievements
                                </CardTitle>
                                <CardDescription>
                                    Your latest accomplishments
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {recentAchievements.length > 0 ? (
                                    recentAchievements.map((achievement) => (
                                        <motion.div
                                            key={achievement.id}
                                            className="flex items-center gap-3 p-3 border rounded-lg"
                                            whileHover={{ scale: 1.02 }}
                                        >
                                            <div 
                                                className="w-10 h-10 rounded-full flex items-center justify-center"
                                                style={{ backgroundColor: achievement.badge_color + '20', color: achievement.badge_color }}
                                            >
                                                <Star className="h-5 w-5" />
                                            </div>
                                            <div className="flex-1">
                                                <h4 className="font-medium text-sm">{achievement.title}</h4>
                                                <p className="text-xs text-gray-500 mt-1">
                                                    {achievement.points} points • {new Date(achievement.unlocked_at).toLocaleDateString()}
                                                </p>
                                            </div>
                                        </motion.div>
                                    ))
                                ) : (
                                    <div className="text-center py-8 text-gray-500">
                                        <Trophy className="h-12 w-12 mx-auto mb-3 opacity-50" />
                                        <p>No achievements yet</p>
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