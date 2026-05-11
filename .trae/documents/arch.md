
## 1. Architecture Design
校园墙H5应用采用纯前端架构，使用React + TypeScript + Vite + Tailwind CSS构建，无需后端服务，所有功能均为前端演示。

## 2. Technology Description
- **前端**: React@18 + TypeScript + tailwindcss@3 + vite + react-router-dom@6 + lucide-react
- **初始化工具**: vite-init
- **后端**: 无（纯前端演示）
- **数据库**: 无（使用模拟数据）

## 3. Route Definitions
| Route | Purpose |
|-------|---------|
| / | 首页 - 动态列表 |
| /post | 发布页 - 内容编辑 |
| /detail/:id | 详情页 - 动态详情 |
| /messages | 消息页 - 通知列表 |
| /profile | 个人中心 - 用户信息 |

## 4. Data Model (Mock Data Structure)
### 4.1 Post Model
```typescript
interface Post {
  id: string;
  userId: string;
  username: string;
  avatar: string;
  content: string;
  images: string[];
  topic: string;
  likes: number;
  comments: number;
  createdAt: string;
  isLiked: boolean;
}
```

### 4.2 Comment Model
```typescript
interface Comment {
  id: string;
  userId: string;
  username: string;
  avatar: string;
  content: string;
  createdAt: string;
}
```

### 4.3 Message Model
```typescript
interface Message {
  id: string;
  type: 'like' | 'comment' | 'system';
  title: string;
  content: string;
  isRead: boolean;
  createdAt: string;
}
```

## 5. File Structure
```
/workspace
├── src/
│   ├── components/
│   │   ├── PostCard.tsx      # 动态卡片组件
│   │   ├── BottomNav.tsx     # 底部导航组件
│   │   └── CommentItem.tsx   # 评论项组件
│   ├── pages/
│   │   ├── Home.tsx          # 首页
│   │   ├── Post.tsx          # 发布页
│   │   ├── Detail.tsx        # 详情页
│   │   ├── Messages.tsx      # 消息页
│   │   └── Profile.tsx       # 个人中心
│   ├── App.tsx               # 主应用组件
│   ├── main.tsx              # 入口文件
│   └── index.css             # 全局样式
├── index.html
├── package.json
├── tsconfig.json
├── vite.config.ts
├── tailwind.config.js
└── postcss.config.js
```

