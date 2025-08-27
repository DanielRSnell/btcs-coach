import { Head } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { 
    Users, 
    BookOpen, 
    TrendingUp, 
    Clock, 
    Target, 
    Award,
    BarChart3,
    PieChart,
    Activity,
    CheckCircle2
} from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart as RechartsPieChart, Cell, LineChart, Line, AreaChart, Area, Pie } from 'recharts';

interface AnalyticsData {
    overview: {
        totalUsers: number;
        activeUsers: number;
        totalModules: number;
        completedSessions: number;
        avgSessionDuration: number;
        totalActionItems: number;
        completedActionItems: number;
        totalAchievements: number;
    };
    userGrowth: Array<{
        month: string;
        users: number;
        active: number;
    }>;
    moduleEngagement: Array<{
        module: string;
        sessions: number;
        completionRate: number;
        avgDuration: number;
    }>;
    piPatterns: Array<{
        pattern: string;
        count: number;
        percentage: number;
    }>;
    sessionTrends: Array<{
        date: string;
        sessions: number;
        duration: number;
    }>;
}

interface AnalyticsPageProps {
    analytics: AnalyticsData;
}

const COLORS = ['#4F46E5', '#7C3AED', '#06B6D4', '#10B981', '#F59E0B', '#EF4444'];

export default function Analytics({ analytics }: AnalyticsPageProps) {
    const { overview, userGrowth, moduleEngagement, piPatterns, sessionTrends } = analytics;

    return (
        <AppLayout>
            <Head title="Analytics Dashboard" />
            
            <div className="py-6 space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                        <p className="text-gray-600 mt-1">Comprehensive insights into BTCS Coach performance</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <Badge variant="secondary" className="bg-green-100 text-green-800">
                            <Activity className="h-3 w-3 mr-1" />
                            Live Data
                        </Badge>
                    </div>
                </div>

                {/* Overview Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Users</CardTitle>
                            <Users className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{overview.totalUsers}</div>
                            <p className="text-xs text-muted-foreground">
                                {overview.activeUsers} active this month
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Sessions Completed</CardTitle>
                            <CheckCircle2 className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{overview.completedSessions}</div>
                            <p className="text-xs text-muted-foreground">
                                {overview.totalModules} total modules
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Avg Session Time</CardTitle>
                            <Clock className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{overview.avgSessionDuration}m</div>
                            <p className="text-xs text-muted-foreground">
                                Per coaching session
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Action Items</CardTitle>
                            <Target className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{overview.completedActionItems}</div>
                            <p className="text-xs text-muted-foreground">
                                of {overview.totalActionItems} completed
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Charts and Detailed Analytics */}
                <Tabs defaultValue="engagement" className="w-full">
                    <TabsList className="grid w-full grid-cols-4">
                        <TabsTrigger value="engagement">Engagement</TabsTrigger>
                        <TabsTrigger value="users">Users</TabsTrigger>
                        <TabsTrigger value="patterns">PI Patterns</TabsTrigger>
                        <TabsTrigger value="trends">Trends</TabsTrigger>
                    </TabsList>

                    <TabsContent value="engagement" className="space-y-6 mt-6">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {/* Module Engagement Chart */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <BarChart3 className="h-5 w-5" />
                                        Module Engagement
                                    </CardTitle>
                                    <CardDescription>
                                        Sessions completed per module
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ResponsiveContainer width="100%" height={300}>
                                        <BarChart data={moduleEngagement}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis 
                                                dataKey="module" 
                                                tick={{ fontSize: 12 }}
                                                angle={-45}
                                                textAnchor="end"
                                                height={80}
                                            />
                                            <YAxis />
                                            <Tooltip />
                                            <Bar dataKey="sessions" fill="#4F46E5" />
                                        </BarChart>
                                    </ResponsiveContainer>
                                </CardContent>
                            </Card>

                            {/* Completion Rates */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <TrendingUp className="h-5 w-5" />
                                        Completion Rates
                                    </CardTitle>
                                    <CardDescription>
                                        Module completion percentages
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ResponsiveContainer width="100%" height={300}>
                                        <BarChart data={moduleEngagement}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis 
                                                dataKey="module" 
                                                tick={{ fontSize: 12 }}
                                                angle={-45}
                                                textAnchor="end"
                                                height={80}
                                            />
                                            <YAxis domain={[0, 100]} />
                                            <Tooltip formatter={(value) => [`${value}%`, 'Completion Rate']} />
                                            <Bar dataKey="completionRate" fill="#10B981" />
                                        </BarChart>
                                    </ResponsiveContainer>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Module Details Table */}
                        <Card>
                            <CardHeader>
                                <CardTitle>Module Performance Details</CardTitle>
                                <CardDescription>
                                    Detailed breakdown of module engagement metrics
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-sm">
                                        <thead>
                                            <tr className="border-b">
                                                <th className="text-left p-2">Module</th>
                                                <th className="text-right p-2">Sessions</th>
                                                <th className="text-right p-2">Completion Rate</th>
                                                <th className="text-right p-2">Avg Duration</th>
                                                <th className="text-center p-2">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {moduleEngagement.map((module, index) => (
                                                <tr key={index} className="border-b hover:bg-gray-50">
                                                    <td className="p-2 font-medium">{module.module}</td>
                                                    <td className="p-2 text-right">{module.sessions}</td>
                                                    <td className="p-2 text-right">{module.completionRate}%</td>
                                                    <td className="p-2 text-right">{module.avgDuration}m</td>
                                                    <td className="p-2 text-center">
                                                        <Badge 
                                                            variant={module.completionRate >= 75 ? "default" : module.completionRate >= 50 ? "secondary" : "destructive"}
                                                            className="text-xs"
                                                        >
                                                            {module.completionRate >= 75 ? "Excellent" : module.completionRate >= 50 ? "Good" : "Needs Attention"}
                                                        </Badge>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <TabsContent value="users" className="space-y-6 mt-6">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {/* User Growth Chart */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <TrendingUp className="h-5 w-5" />
                                        User Growth
                                    </CardTitle>
                                    <CardDescription>
                                        Total and active users over time
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ResponsiveContainer width="100%" height={300}>
                                        <AreaChart data={userGrowth}>
                                            <CartesianGrid strokeDasharray="3 3" />
                                            <XAxis dataKey="month" />
                                            <YAxis />
                                            <Tooltip />
                                            <Area type="monotone" dataKey="users" stackId="1" stroke="#4F46E5" fill="#4F46E5" fillOpacity={0.6} />
                                            <Area type="monotone" dataKey="active" stackId="2" stroke="#10B981" fill="#10B981" fillOpacity={0.8} />
                                        </AreaChart>
                                    </ResponsiveContainer>
                                </CardContent>
                            </Card>

                            {/* User Activity Stats */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Activity className="h-5 w-5" />
                                        User Activity
                                    </CardTitle>
                                    <CardDescription>
                                        Current user engagement metrics
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-gray-600">Active Users</span>
                                        <span className="text-lg font-semibold">{overview.activeUsers}</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            className="bg-green-500 h-2 rounded-full" 
                                            style={{ width: `${(overview.activeUsers / overview.totalUsers) * 100}%` }}
                                        ></div>
                                    </div>
                                    
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-gray-600">Session Completion</span>
                                        <span className="text-lg font-semibold">
                                            {Math.round((overview.completedSessions / (overview.totalUsers * overview.totalModules)) * 100)}%
                                        </span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            className="bg-blue-500 h-2 rounded-full" 
                                            style={{ width: `${(overview.completedSessions / (overview.totalUsers * overview.totalModules)) * 100}%` }}
                                        ></div>
                                    </div>

                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-gray-600">Action Item Completion</span>
                                        <span className="text-lg font-semibold">
                                            {Math.round((overview.completedActionItems / overview.totalActionItems) * 100)}%
                                        </span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            className="bg-purple-500 h-2 rounded-full" 
                                            style={{ width: `${(overview.completedActionItems / overview.totalActionItems) * 100}%` }}
                                        ></div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <TabsContent value="patterns" className="space-y-6 mt-6">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {/* PI Patterns Distribution */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <PieChart className="h-5 w-5" />
                                        PI Pattern Distribution
                                    </CardTitle>
                                    <CardDescription>
                                        Breakdown of user behavioral patterns
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ResponsiveContainer width="100%" height={300}>
                                        <RechartsPieChart>
                                            <Pie
                                                data={piPatterns}
                                                cx="50%"
                                                cy="50%"
                                                labelLine={false}
                                                label={({ pattern, percentage }) => `${pattern}: ${percentage}%`}
                                                outerRadius={80}
                                                fill="#8884d8"
                                                dataKey="count"
                                            >
                                                {piPatterns.map((entry, index) => (
                                                    <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                                                ))}
                                            </Pie>
                                            <Tooltip />
                                        </RechartsPieChart>
                                    </ResponsiveContainer>
                                </CardContent>
                            </Card>

                            {/* PI Patterns Table */}
                            <Card>
                                <CardHeader>
                                    <CardTitle>Pattern Details</CardTitle>
                                    <CardDescription>
                                        Detailed breakdown of PI behavioral patterns
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {piPatterns.map((pattern, index) => (
                                            <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                                                <div className="flex items-center gap-3">
                                                    <div 
                                                        className="w-4 h-4 rounded-full" 
                                                        style={{ backgroundColor: COLORS[index % COLORS.length] }}
                                                    ></div>
                                                    <span className="font-medium">{pattern.pattern}</span>
                                                </div>
                                                <div className="text-right">
                                                    <div className="font-semibold">{pattern.count} users</div>
                                                    <div className="text-sm text-gray-500">{pattern.percentage}%</div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <TabsContent value="trends" className="space-y-6 mt-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Activity className="h-5 w-5" />
                                    Session Trends
                                </CardTitle>
                                <CardDescription>
                                    Daily session activity and duration trends
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ResponsiveContainer width="100%" height={400}>
                                    <BarChart data={sessionTrends}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="date" />
                                        <YAxis yAxisId="left" />
                                        <YAxis yAxisId="right" orientation="right" />
                                        <Tooltip />
                                        <Bar yAxisId="left" dataKey="sessions" fill="#4F46E5" />
                                        <Line yAxisId="right" type="monotone" dataKey="duration" stroke="#10B981" strokeWidth={2} />
                                    </BarChart>
                                </ResponsiveContainer>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}